<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'user_id', 'name', 'company_name', 'contact_name', 'phone', 'email', 'address',
        'city', 'region', 'payment_terms', 'delivery_delay_days', 'status', 'rating', 'notes',
    ];

    public const STATUSES = [
        'en_attente' => 'En attente',
        'actif' => 'Actif',
        'suspendu' => 'Suspendu',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supplierProducts(): HasMany
    {
        return $this->hasMany(SupplierProduct::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function totalOwed(): float
    {
        return (float) $this->purchaseOrders()
            ->whereNotIn('status', ['brouillon', 'annulee'])
            ->sum('total');
    }

    public function totalPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function balance(): float
    {
        return $this->totalOwed() - $this->totalPaid();
    }
}
