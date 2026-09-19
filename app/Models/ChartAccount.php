<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChartAccount extends Model
{
    protected $table = 'chart_of_accounts';

    protected $fillable = ['code', 'name', 'class', 'parent_id', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const CLASSES = [
        1 => 'Classe 1 — Comptes de capitaux',
        2 => 'Classe 2 — Comptes d\'immobilisations',
        3 => 'Classe 3 — Comptes de stocks',
        4 => 'Classe 4 — Comptes de tiers',
        5 => 'Classe 5 — Comptes de trésorerie',
        6 => 'Classe 6 — Comptes de charges',
        7 => 'Classe 7 — Comptes de produits',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChartAccount::class, 'parent_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function totalDebit(): float
    {
        return (float) $this->lines()->sum('debit');
    }

    public function totalCredit(): float
    {
        return (float) $this->lines()->sum('credit');
    }

    public function balance(): float
    {
        return $this->totalDebit() - $this->totalCredit();
    }

    public function isDebitNormal(): bool
    {
        return in_array((int) $this->class, [2, 3, 5, 6], true);
    }
}
