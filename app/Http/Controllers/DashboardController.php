<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoicePart;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalRepairs = Invoice::count();
        $activeRepairs = Invoice::where('repair_status', 'Active')->count();
        $inactiveRepairs = Invoice::where('repair_status', 'Not Active')->count();

        $totalQuoted = (int) Invoice::sum('repair_quote');

        $totalPartsCost = (int) InvoicePart::query()
            ->selectRaw('COALESCE(SUM(quantity * unit_price), 0) as total')
            ->value('total');

        $revenue = $totalQuoted - $totalPartsCost;

        $recentRepairs = Invoice::query()
            ->latest()
            ->take(6)
            ->get(['id', 'invoice_number', 'client_name', 'product_category', 'repair_quote', 'repair_status', 'created_at']);

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_repairs' => $totalRepairs,
                'active_repairs' => $activeRepairs,
                'inactive_repairs' => $inactiveRepairs,
                'revenue' => $revenue,
            ],
            'recentRepairs' => $recentRepairs,
        ]);
    }
}
