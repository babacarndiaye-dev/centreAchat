<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    protected $fillable = [
        'name', 'cities', 'fee', 'free_above', 'delay_days', 'position', 'is_active',
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'free_above' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function feeFor(float $subtotal): float
    {
        if ($this->free_above && $subtotal >= (float) $this->free_above) {
            return 0;
        }

        return (float) $this->fee;
    }
}
