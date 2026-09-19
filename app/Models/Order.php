<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'channel', 'customer_name', 'customer_email', 'customer_phone',
        'delivery_address', 'city', 'hotel_name', 'room_number', 'gift_message', 'delivery_zone_id', 'status', 'subtotal', 'discount_amount',
        'tax_amount', 'delivery_fee', 'total', 'payment_method', 'payment_status', 'invoice_due_date', 'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'invoice_due_date' => 'date',
    ];

    public const STATUSES = [
        'brouillon' => 'Brouillon',
        'nouvelle' => 'Nouvelle',
        'confirmee' => 'Confirmée',
        'payee' => 'Payée',
        'en_preparation' => 'En préparation',
        'prete' => 'Prête',
        'en_livraison' => 'En livraison',
        'livree' => 'Livrée',
        'terminee' => 'Terminée',
        'annulee' => 'Annulée',
        'remboursee' => 'Remboursée',
        'en_attente_paiement' => 'En attente de paiement',
    ];

    public const CHANNELS = [
        'site_web' => 'Site web',
        'boutique' => 'Boutique physique',
        'b2b' => 'B2B',
        'gros' => 'Vente en gros',
        'telephone' => 'Téléphone',
        'agent' => 'Saisie agent',
    ];

    public const PAYMENT_STATUSES = [
        'en_attente' => 'En attente',
        'partiellement_paye' => 'Partiellement payé',
        'paye' => 'Payé',
        'echoue' => 'Échoué',
        'rembourse' => 'Remboursé',
    ];

    public function deliveryZone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function posPayments(): HasMany
    {
        return $this->hasMany(PosSalePayment::class);
    }

    public function posReturns(): HasMany
    {
        return $this->hasMany(PosReturn::class);
    }

    public function amountPaid(): float
    {
        return (float) $this->posPayments()->sum('amount');
    }

    public function amountRefunded(): float
    {
        return (float) $this->posReturns()->sum('total_refund');
    }

    public static function generateOrderNumber(): string
    {
        return 'CA-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -6));
    }
}
