<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReception extends Model
{
    protected $fillable = [
        'purchase_order_id', 'received_by', 'reception_date', 'quality_status', 'notes',
    ];

    protected $casts = [
        'reception_date' => 'date',
    ];

    public const QUALITY_STATUSES = [
        'conforme' => 'Conforme',
        'non_conforme' => 'Non conforme',
        'partielle' => 'Conformité partielle',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseReceptionItem::class);
    }
}
