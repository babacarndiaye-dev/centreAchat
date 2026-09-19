<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringOrder extends Model
{
    protected $fillable = [
        'user_id', 'frequency', 'delivery_address', 'city', 'payment_method',
        'status', 'next_run_date', 'last_run_date', 'notes',
    ];

    protected $casts = [
        'next_run_date' => 'date',
        'last_run_date' => 'date',
    ];

    public const FREQUENCIES = [
        'hebdomadaire' => 'Chaque semaine',
        'bimensuelle' => 'Toutes les deux semaines',
        'mensuelle' => 'Chaque mois',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RecurringOrderItem::class);
    }

    public function computeNextRunDate(): \Illuminate\Support\Carbon
    {
        return match ($this->frequency) {
            'hebdomadaire' => now()->addWeek(),
            'bimensuelle' => now()->addWeeks(2),
            'mensuelle' => now()->addMonth(),
        };
    }
}
