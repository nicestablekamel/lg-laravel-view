<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'company_name',
        'company_phone',
        'company_email',
        'company_address',
        'client_name',
        'client_phone',
        'reception_date',
        'recuperation_date',
        'product_category',
        'serial_number',
        'problem_description',
        'repair_quote',
        'repair_status',
        'policy_note',
    ];

    protected $casts = [
        'reception_date' => 'date:Y-m-d',
        'recuperation_date' => 'date:Y-m-d',
        'repair_quote' => 'integer',
    ];

    /**
     * Parts consumed by this repair. Each row snapshots the unit price at
     * the time it was used (see InvoicePart), so later price changes on
     * the Part don't retroactively rewrite past invoices.
     */
    public function parts(): HasMany
    {
        return $this->hasMany(InvoicePart::class);
    }

    /**
     * Preview the invoice number that will be assigned next (shown on the
     * form before saving). The real number is (re)computed inside a locked
     * transaction in the controller to avoid a race between two people
     * saving at the same time.
     */
    public static function nextInvoiceNumber(): string
    {
        $next = (static::max('id') ?? 0) + 1;

        return 'INV-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Atomically compute and reserve the next invoice number. Call this
     * from inside a DB::transaction() right before creating the row.
     */
    public static function lockNextInvoiceNumber(): string
    {
        $next = (static::lockForUpdate()->max('id') ?? 0) + 1;

        return 'INV-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
