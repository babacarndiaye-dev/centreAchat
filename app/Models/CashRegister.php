<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegister extends Model
{
    protected $fillable = [
        'opened_by', 'closed_by', 'opening_float', 'actual_closing_amount', 'status', 'closed_at', 'notes',
    ];

    protected $casts = [
        'opening_float' => 'decimal:2',
        'actual_closing_amount' => 'decimal:2',
        'closed_at' => 'datetime',
    ];

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    public function salePayments(): HasMany
    {
        return $this->hasMany(PosSalePayment::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(PosReturn::class);
    }

    public function cashSalesTotal(): float
    {
        return (float) $this->salePayments()->where('method', 'especes')->sum('amount');
    }

    public function cashRefundsTotal(): float
    {
        return (float) $this->returns()->sum('total_refund');
    }

    public function encaissementsTotal(): float
    {
        return (float) $this->movements()->where('type', 'encaissement')->sum('amount');
    }

    public function decaissementsTotal(): float
    {
        return (float) $this->movements()->where('type', 'decaissement')->sum('amount');
    }

    public function theoreticalCash(): float
    {
        return (float) $this->opening_float
            + $this->cashSalesTotal()
            + $this->encaissementsTotal()
            - $this->decaissementsTotal()
            - $this->cashRefundsTotal();
    }

    public function variance(): ?float
    {
        if (is_null($this->actual_closing_amount)) {
            return null;
        }

        return (float) $this->actual_closing_amount - $this->theoreticalCash();
    }
}
