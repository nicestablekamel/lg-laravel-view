<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PartController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $onlyLowStock = $request->boolean('low_stock');

        $parts = Part::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($onlyLowStock, fn ($query) => $query->lowStock())
            ->orderBy('name')
            ->get();

        return Inertia::render('stock/Index', [
            'parts' => $parts,
            'filters' => [
                'search' => $search,
                'low_stock' => $onlyLowStock,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:parts,sku'],
            'unit' => ['nullable', 'string', 'max:50'],
            'quantity_on_hand' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'unit_price' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data) {
            $part = Part::create($data + ['unit' => $data['unit'] ?: 'pièce']);

            if ($part->quantity_on_hand > 0) {
                StockMovement::create([
                    'part_id' => $part->id,
                    'type' => 'in',
                    'quantity' => $part->quantity_on_hand,
                    'reason' => 'Stock initial',
                ]);
            }
        });

        return back();
    }

    public function update(Request $request, Part $part): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:parts,sku,' . $part->id],
            'unit' => ['nullable', 'string', 'max:50'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'unit_price' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        // Quantity is intentionally NOT editable here — it only changes via
        // restock() or automatically when an invoice consumes stock, so
        // every change stays traceable in stock_movements.
        $part->update($data + ['unit' => $data['unit'] ?: 'pièce']);

        return back();
    }

    public function restock(Request $request, Part $part): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($part, $data) {
            $locked = Part::whereKey($part->id)->lockForUpdate()->firstOrFail();
            $locked->increment('quantity_on_hand', $data['quantity']);

            StockMovement::create([
                'part_id' => $locked->id,
                'type' => 'in',
                'quantity' => $data['quantity'],
                'reason' => $data['reason'] ?? 'Réapprovisionnement' ,
            ]);
        });

        return back();
    }

    public function destroy(Part $part): RedirectResponse
    {
        if ($part->invoiceLines()->exists()) {
            return back()->withErrors([
                'part' => 'Cette pièce a déjà été utilisée sur une décharge et ne peut pas être supprimée.',
            ]);
        }

        $part->delete();

        return back();
    }
}
