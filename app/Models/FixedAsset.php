<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedAsset extends Model
{
    protected $fillable = [
        'category', 'name', 'acquisition_date', 'acquisition_value', 'useful_life_years',
        'depreciation_method', 'payment_account_id', 'supplier_id', 'status',
        'disposal_date', 'disposal_value', 'notes',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'acquisition_value' => 'decimal:2',
        'disposal_date' => 'date',
        'disposal_value' => 'decimal:2',
    ];

    public const CATEGORIES = [
        'vehicule' => 'Véhicule',
        'ordinateur' => 'Ordinateur / Matériel informatique',
        'mobilier' => 'Mobilier',
        'equipement' => 'Équipement',
        'materiel' => 'Matériel',
        'autre' => 'Autre',
    ];

    public const METHODS = [
        'lineaire' => 'Linéaire',
        'degressif' => 'Dégressif',
    ];

    public const STATUSES = [
        'en_service' => 'En service',
        'cede' => 'Cédé',
        'reforme' => 'Réformé',
    ];

    public function paymentAccount(): BelongsTo
    {
        return $this->belongsTo(PaymentAccount::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    protected function degressifCoefficient(): float
    {
        return match (true) {
            $this->useful_life_years <= 4 => 1.5,
            $this->useful_life_years <= 6 => 2.0,
            default => 2.5,
        };
    }

    /**
     * Full amortization schedule, one row per fiscal year of useful life.
     */
    public function depreciationSchedule(): array
    {
        $value = (float) $this->acquisition_value;
        $years = $this->useful_life_years;
        $schedule = [];
        $accumulated = 0;

        if ($this->depreciation_method === 'lineaire') {
            $annual = $value / $years;

            for ($i = 1; $i <= $years; $i++) {
                $accumulated = min($value, $accumulated + $annual);
                $schedule[] = [
                    'year' => $i,
                    'annual' => $annual,
                    'accumulated' => $accumulated,
                    'net_value' => $value - $accumulated,
                ];
            }

            return $schedule;
        }

        // Dégressif with switch to straight-line once it becomes more favorable.
        $rate = (1 / $years) * $this->degressifCoefficient();
        $remainingYears = $years;
        $netValue = $value;

        for ($i = 1; $i <= $years; $i++) {
            $degressifAmount = $netValue * $rate;
            $lineaireAmount = $netValue / $remainingYears;
            $annual = max($degressifAmount, $lineaireAmount);
            $annual = min($annual, $netValue);

            $accumulated += $annual;
            $netValue -= $annual;
            $remainingYears--;

            $schedule[] = [
                'year' => $i,
                'annual' => $annual,
                'accumulated' => $accumulated,
                'net_value' => max(0, $netValue),
            ];
        }

        return $schedule;
    }

    public function yearsElapsed(): float
    {
        $end = $this->status === 'en_service' ? now() : ($this->disposal_date ?? now());

        return max(0, min($this->useful_life_years, $this->acquisition_date->diffInMonths($end) / 12));
    }

    public function accumulatedDepreciation(): float
    {
        $schedule = $this->depreciationSchedule();
        $yearsElapsed = $this->yearsElapsed();
        $fullYears = (int) floor($yearsElapsed);
        $partialYear = $yearsElapsed - $fullYears;

        $accumulated = $fullYears > 0 ? ($schedule[$fullYears - 1]['accumulated'] ?? end($schedule)['accumulated']) : 0;

        if ($partialYear > 0 && isset($schedule[$fullYears])) {
            $accumulated += $schedule[$fullYears]['annual'] * $partialYear;
        }

        return round(min((float) $this->acquisition_value, $accumulated), 2);
    }

    public function netBookValue(): float
    {
        return round((float) $this->acquisition_value - $this->accumulatedDepreciation(), 2);
    }

    public function annualDepreciation(): float
    {
        return $this->depreciationSchedule()[0]['annual'] ?? 0;
    }

    public function isFullyDepreciated(): bool
    {
        return $this->netBookValue() <= 0.5;
    }
}
