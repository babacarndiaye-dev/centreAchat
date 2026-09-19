<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankStatementLine extends Model
{
    protected $fillable = [
        'bank_statement_import_id', 'payment_account_id', 'statement_date', 'description',
        'amount', 'reference', 'matched_transaction_id', 'status',
    ];

    protected $casts = [
        'statement_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public const STATUSES = [
        'non_rapproche' => 'Non rapproché',
        'rapproche' => 'Rapproché',
        'ecart' => 'Écart',
    ];

    public function import(): BelongsTo
    {
        return $this->belongsTo(BankStatementImport::class, 'bank_statement_import_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(PaymentAccount::class, 'payment_account_id');
    }

    public function matchedTransaction(): BelongsTo
    {
        return $this->belongsTo(PaymentAccountTransaction::class, 'matched_transaction_id');
    }
}
