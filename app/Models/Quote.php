<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    protected $fillable = [
        'quote_number', 'user_id', 'status', 'total', 'valid_until', 'notes', 'order_id',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'valid_until' => 'date',
    ];

    public const STATUSES = [
        'en_attente' => 'En attente de traitement',
        'envoye' => 'Devis envoyé',
        'accepte' => 'Accepté',
        'refuse' => 'Refusé',
        'expire' => 'Expiré',
        'converti' => 'Converti en commande',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function generateNumber(): string
    {
        return 'DEV-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -5));
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'accepte', 'converti' => 'admin-badge-success',
            'refuse' => 'admin-badge-danger',
            'expire' => 'admin-badge-neutral',
            default => 'admin-badge-warning',
        };
    }
}
