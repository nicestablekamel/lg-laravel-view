<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Part extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'unit',
        'quantity_on_hand',
        'low_stock_threshold',
        'unit_price',
        'notes',
    ];

    protected $casts = [
        'quantity_on_hand' => 'integer',
        'low_stock_threshold' => 'integer',
        'unit_price' => 'integer',
    ];

    protected $appends = ['is_low_stock'];

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function invoiceLines(): HasMany
    {
        return $this->hasMany(InvoicePart::class);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('quantity_on_hand', '<=', 'low_stock_threshold');
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity_on_hand <= $this->low_stock_threshold;
    }
}
