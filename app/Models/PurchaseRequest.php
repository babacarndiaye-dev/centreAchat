<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequest extends Model
{
    protected $fillable = [
        'reference', 'requested_by', 'validated_by', 'status', 'reason', 'needed_by_date', 'validated_at',
    ];

    protected $casts = [
        'needed_by_date' => 'date',
        'validated_at' => 'datetime',
    ];

    public const STATUSES = [
        'brouillon' => 'Brouillon',
        'en_attente_validation' => 'En attente de validation',
        'validee' => 'Validée',
        'rejetee' => 'Rejetée',
        'convertie' => 'Convertie en bon de commande',
        'annulee' => 'Annulée',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public static function generateReference(): string
    {
        return 'DA-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -5));
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'validee', 'convertie' => 'admin-badge-success',
            'rejetee', 'annulee' => 'admin-badge-danger',
            'brouillon' => 'admin-badge-neutral',
            default => 'admin-badge-warning',
        };
    }
}
