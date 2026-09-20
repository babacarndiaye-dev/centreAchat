<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'order_number', 'supplier_id', 'purchase_request_id', 'created_by', 'status',
        'order_date', 'expected_date', 'subtotal', 'total', 'invoice_reference', 'amount_paid', 'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public const STATUSES = [
        'brouillon' => 'Brouillon',
        'envoyee' => 'Envoyée au fournisseur',
        'confirmee' => 'Confirmée',
        'partiellement_recue' => 'Partiellement reçue',
        'recue' => 'Reçue',
        'annulee' => 'Annulée',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function receptions(): HasMany
    {
        return $this->hasMany(PurchaseReception::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function balance(): float
    {
        return (float) $this->total - (float) $this->amount_paid;
    }

    public function isFullyReceived(): bool
    {
        return $this->items->every(fn ($item) => $item->quantity_received >= $item->quantity_ordered);
    }

    public function isPartiallyReceived(): bool
    {
        return $this->items->contains(fn ($item) => $item->quantity_received > 0);
    }

    public static function generateOrderNumber(): string
    {
        return 'BC-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -5));
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'recue' => 'admin-badge-success',
            'annulee' => 'admin-badge-danger',
            'brouillon' => 'admin-badge-neutral',
            default => 'admin-badge-warning',
        };
    }
}
