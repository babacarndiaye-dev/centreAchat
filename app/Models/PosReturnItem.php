<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosReturnItem extends Model
{
    protected $fillable = ['pos_return_id', 'order_item_id', 'quantity', 'unit_price', 'total'];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function posReturn(): BelongsTo
    {
        return $this->belongsTo(PosReturn::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
