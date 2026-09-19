<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringOrderItem extends Model
{
    protected $fillable = ['recurring_order_id', 'product_id', 'quantity'];

    public function recurringOrder(): BelongsTo
    {
        return $this->belongsTo(RecurringOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
