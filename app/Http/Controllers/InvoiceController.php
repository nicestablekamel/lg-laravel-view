<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Part;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));

        $invoices = Invoice::query()
            ->with('parts.part')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhere('client_name', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('invoices/History', [
            'invoices' => $invoices,
            'filters' => ['search' => $search],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('invoices/Create', [
            'nextInvoiceNumber' => Invoice::nextInvoiceNumber(),
            'parts' => Part::query()
                ->orderBy('name')
                ->get(['id', 'name', 'sku', 'unit', 'quantity_on_hand', 'unit_price']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:255'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:255'],

            'client_name' => ['required', 'string', 'max:255'],
            'client_phone' => ['required', 'string', 'max:255'],
            'reception_date' => ['required', 'date'],
            'recuperation_date' => ['required', 'date'],

            'product_category' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255'],
            'problem_description' => ['required', 'string'],

            'repair_quote' => ['required', 'integer', 'min:0'],
            'repair_status' => ['required', 'in:Active,Not Active'],
            'policy_note' => ['nullable', 'string'],

            'parts' => ['nullable', 'array'],
            'parts.*.part_id' => ['required', 'integer', 'exists:parts,id'],
            'parts.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $partLines = collect($data['parts'] ?? [])
            // merge duplicate rows for the same part into one line
            ->groupBy('part_id')
            ->map(fn ($rows, $partId) => [
                'part_id' => (int) $partId,
                'quantity' => (int) $rows->sum('quantity'),
            ])
            ->values();

        unset($data['parts']);

        $invoice = DB::transaction(function () use ($data, $partLines) {
            $invoice = Invoice::create($data + [
                'invoice_number' => Invoice::lockNextInvoiceNumber(),
            ]);

            foreach ($partLines as $line) {
                // Lock the part row before checking stock, so two invoices
                // saved at the same instant can't both pass the check and
                // oversell the same units.
                $part = Part::whereKey($line['part_id'])->lockForUpdate()->firstOrFail();

                if ($part->quantity_on_hand < $line['quantity']) {
                    throw ValidationException::withMessages([
                        'parts' => "Stock insuffisant pour « {$part->name} » (disponible : {$part->quantity_on_hand}, demandé : {$line['quantity']}).",
                    ]);
                }

                $part->decrement('quantity_on_hand', $line['quantity']);

                $invoice->parts()->create([
                    'part_id' => $part->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $part->unit_price,
                ]);

                StockMovement::create([
                    'part_id' => $part->id,
                    'type' => 'out',
                    'quantity' => $line['quantity'],
                    'reason' => "Décharge {$invoice->invoice_number}",
                    'invoice_id' => $invoice->id,
                ]);
            }

            return $invoice;
        });

        return back()->with([
            'invoice' => $invoice,
            'nextInvoiceNumber' => Invoice::nextInvoiceNumber(),
            'parts' => Part::query()
                ->orderBy('name')
                ->get(['id', 'name', 'sku', 'unit', 'quantity_on_hand', 'unit_price']),
        ]);
    }
}
