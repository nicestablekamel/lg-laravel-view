<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\DashboardController;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('parts', [PartController::class, 'index'])->name('parts.index');
    Route::post('parts', [PartController::class, 'store'])->name('parts.store');
    Route::put('parts/{part}', [PartController::class, 'update'])->name('parts.update');
    Route::delete('parts/{part}', [PartController::class, 'destroy'])->name('parts.destroy');
    Route::post('parts/{part}/restock', [PartController::class, 'restock'])->name('parts.restock');
    
});



require __DIR__.'/settings.php';
