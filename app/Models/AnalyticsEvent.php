<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    const UPDATED_AT = null;

    protected $fillable = ['session_id', 'event_type', 'product_id', 'order_id', 'meta'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AnalyticsSession::class, 'session_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
