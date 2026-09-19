<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAccountTransaction extends Model
{
    protected $fillable = [
        'payment_account_id', 'type', 'amount', 'category', 'description', 'reference',
        'transaction_date', 'is_reconciled', 'reconciled_at', 'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'is_reconciled' => 'boolean',
        'reconciled_at' => 'datetime',
    ];

    public function signedAmount(): float
    {
        return $this->type === 'entree' ? (float) $this->amount : -(float) $this->amount;
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(PaymentAccount::class, 'payment_account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
