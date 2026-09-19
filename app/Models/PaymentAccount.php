<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentAccount extends Model
{
    protected $fillable = [
        'name', 'type', 'chart_account_id', 'provider', 'account_number', 'initial_balance', 'is_active', 'notes',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public const TYPES = [
        'banque' => 'Compte bancaire',
        'mobile_money' => 'Mobile Money',
        'caisse' => 'Caisse',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentAccountTransaction::class);
    }

    public function chartAccount(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ChartAccount::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function statementImports(): HasMany
    {
        return $this->hasMany(BankStatementImport::class);
    }

    public function statementLines(): HasMany
    {
        return $this->hasMany(BankStatementLine::class);
    }

    public function balance(): float
    {
        $entrees = (float) $this->transactions()->where('type', 'entree')->sum('amount');
        $sorties = (float) $this->transactions()->where('type', 'sortie')->sum('amount');

        return (float) $this->initial_balance + $entrees - $sorties;
    }
}
