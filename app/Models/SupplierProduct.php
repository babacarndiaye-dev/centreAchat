<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierProduct extends Model
{
    protected $fillable = [
        'supplier_id', 'product_id', 'supplier_price', 'supplier_reference', 'lead_time_days', 'is_preferred',
    ];

    protected $casts = [
        'supplier_price' => 'decimal:2',
        'is_preferred' => 'boolean',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
