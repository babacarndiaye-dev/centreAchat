<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    protected $fillable = [
        'code', 'name', 'payment_account_id', 'available_online', 'available_pos', 'requires_b2b', 'position', 'is_active',
    ];

    protected $casts = [
        'available_online' => 'boolean',
        'available_pos' => 'boolean',
        'requires_b2b' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function paymentAccount(): BelongsTo
    {
        return $this->belongsTo(PaymentAccount::class);
    }
}
