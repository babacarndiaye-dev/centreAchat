<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReceptionItem extends Model
{
    protected $fillable = [
        'purchase_reception_id', 'purchase_order_item_id', 'quantity_received', 'quality_status', 'notes',
    ];

    public function reception(): BelongsTo
    {
        return $this->belongsTo(PurchaseReception::class, 'purchase_reception_id');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id');
    }
}
