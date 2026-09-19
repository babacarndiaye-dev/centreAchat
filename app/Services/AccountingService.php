<?php

namespace App\Services;

use App\Models\ChartAccount;
use App\Models\Expense;
use App\Models\FixedAsset;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Order;
use App\Models\PaymentAccount;
use App\Models\PosReturn;
use App\Models\PurchaseReception;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    public const ACCOUNT_CLIENTS = '411';
    public const ACCOUNT_FOURNISSEURS = '401';
    public const ACCOUNT_VENTES = '701';
    public const ACCOUNT_ACHATS = '601';
    public const ACCOUNT_CAISSE = '571';
    public const ACCOUNT_BANQUE = '512';
    public const ACCOUNT_CHARGES_GENERIQUE = '606';
    public const ACCOUNT_IMMOBILISATIONS = '211';

    public function postExpense(Expense $expense): ?JournalEntry
    {
        if ($this->alreadyPosted(Expense::class, $expense->id)) {
            return null;
        }

        if (! $expense->payment_account_id) {
            return null;
        }

        $paymentAccount = $expense->account;
        $chargeAccount = $expense->category->chart_account_id
            ? $expense->category->chartAccount
            : $this->account(self::ACCOUNT_CHARGES_GENERIQUE);
        $treasuryAccount = $paymentAccount?->chartAccount;

        if (! $chargeAccount || ! $treasuryAccount) {
            return null;
        }

        $journal = $this->journalForPaymentAccount($paymentAccount);

        return $this->createEntry(
            journal: $journal,
            date: $expense->expense_date,
            description: 'Dépense : '.$expense->category->name,
            lines: [
                ['account' => $chargeAccount, 'debit' => $expense->amount, 'label' => $expense->beneficiary],
                ['account' => $treasuryAccount, 'credit' => $expense->amount, 'label' => $paymentAccount->name],
            ],
            sourceType: Expense::class,
            sourceId: $expense->id,
            reference: 'DEP-'.$expense->id,
        );
    }

    public function postSupplierPayment(SupplierPayment $payment): ?JournalEntry
    {
        if ($this->alreadyPosted(SupplierPayment::class, $payment->id)) {
            return null;
        }

        if (! $payment->payment_account_id) {
            return null;
        }

        $treasuryAccount = $payment->paymentAccount?->chartAccount;
        $fournisseursAccount = $this->account(self::ACCOUNT_FOURNISSEURS);

        if (! $treasuryAccount || ! $fournisseursAccount) {
            return null;
        }

        $journal = $this->journalForPaymentAccount($payment->paymentAccount);

        return $this->createEntry(
            journal: $journal,
            date: $payment->payment_date,
            description: 'Paiement fournisseur : '.$payment->supplier->name,
            lines: [
                ['account' => $fournisseursAccount, 'debit' => $payment->amount, 'label' => $payment->supplier->name],
                ['account' => $treasuryAccount, 'credit' => $payment->amount, 'label' => $payment->paymentAccount->name],
            ],
            sourceType: SupplierPayment::class,
            sourceId: $payment->id,
            reference: 'PAY-FOUR-'.$payment->id,
        );
    }

    public function postPurchaseReception(PurchaseReception $reception): ?JournalEntry
    {
        if ($this->alreadyPosted(PurchaseReception::class, $reception->id)) {
            return null;
        }

        $amount = $reception->items()
            ->where('quality_status', 'conforme')
            ->get()
            ->sum(fn ($item) => $item->quantity_received * $item->orderItem->unit_price);

        if ($amount <= 0) {
            return null;
        }

        $achatsAccount = $this->account(self::ACCOUNT_ACHATS);
        $fournisseursAccount = $this->account(self::ACCOUNT_FOURNISSEURS);
        $journal = Journal::where('type', 'achats')->first();

        if (! $achatsAccount || ! $fournisseursAccount || ! $journal) {
            return null;
        }

        $supplierName = $reception->purchaseOrder->supplier->name;

        return $this->createEntry(
            journal: $journal,
            date: $reception->reception_date,
            description: 'Réception marchandises : '.$supplierName,
            lines: [
                ['account' => $achatsAccount, 'debit' => $amount, 'label' => $supplierName],
                ['account' => $fournisseursAccount, 'credit' => $amount, 'label' => $supplierName],
            ],
            sourceType: PurchaseReception::class,
            sourceId: $reception->id,
            reference: 'REC-'.$reception->purchaseOrder->order_number,
        );
    }

    /**
     * Posts the treasury receipt for amounts actually collected against an order — one
     * debit line per payment account used (so a mixed cash+mobile-money POS sale is a
     * single balanced entry), credited to Clients (411). Complements postSaleInvoice(),
     * which already recognizes the full revenue regardless of what's actually been paid.
     *
     * @param  array<int, array{account: PaymentAccount, amount: float}>  $paymentLines
     */
    public function postSaleReceipt(Order $order, array $paymentLines): ?JournalEntry
    {
        if ($this->alreadyPosted('order_receipt', $order->id)) {
            return null;
        }

        $clientsAccount = $this->account(self::ACCOUNT_CLIENTS);

        if (! $clientsAccount || empty($paymentLines)) {
            return null;
        }

        $lines = [];
        $total = 0;
        $journal = null;

        foreach ($paymentLines as $line) {
            $account = $line['account'];
            $amount = (float) $line['amount'];
            $treasuryAccount = $account->chartAccount;

            if (! $treasuryAccount || $amount <= 0) {
                continue;
            }

            $lines[] = ['account' => $treasuryAccount, 'debit' => $amount, 'label' => $account->name];
            $total += $amount;
            $journal ??= $this->journalForPaymentAccount($account);
        }

        if (empty($lines) || ! $journal) {
            return null;
        }

        $lines[] = ['account' => $clientsAccount, 'credit' => $total, 'label' => $order->order_number];

        return $this->createEntry(
            journal: $journal,
            date: $order->created_at->toDateString(),
            description: 'Encaissement vente : '.$order->order_number,
            lines: $lines,
            sourceType: 'order_receipt',
            sourceId: $order->id,
            reference: $order->order_number,
        );
    }

    /**
     * Reverses the revenue for a POS return/refund and records the cash leaving the
     * register — the mirror image of postSaleInvoice()/postSaleReceipt() for the
     * refunded amount. Refunds are assumed paid out from the cash register.
     */
    public function postSaleReturn(PosReturn $return): ?JournalEntry
    {
        if ($this->alreadyPosted(PosReturn::class, $return->id)) {
            return null;
        }

        $amount = (float) $return->total_refund;

        if ($amount <= 0) {
            return null;
        }

        $ventesAccount = $this->account(self::ACCOUNT_VENTES);
        $caisseAccount = PaymentAccount::where('type', 'caisse')->first();
        $treasuryAccount = $caisseAccount?->chartAccount ?? $this->account(self::ACCOUNT_CAISSE);

        if (! $ventesAccount || ! $treasuryAccount) {
            return null;
        }

        $journal = ($caisseAccount ? $this->journalForPaymentAccount($caisseAccount) : null) ?? Journal::where('type', 'caisse')->first();

        return $this->createEntry(
            journal: $journal,
            date: now()->toDateString(),
            description: 'Retour / remboursement : commande '.$return->order->order_number,
            lines: [
                ['account' => $ventesAccount, 'debit' => $amount, 'label' => $return->order->order_number],
                ['account' => $treasuryAccount, 'credit' => $amount, 'label' => 'Remboursement caisse'],
            ],
            sourceType: PosReturn::class,
            sourceId: $return->id,
            reference: 'RET-'.$return->id,
        );
    }

    public function postSaleInvoice(Order $order): ?JournalEntry
    {
        if ($this->alreadyPosted('order_invoice', $order->id)) {
            return null;
        }

        $clientsAccount = $this->account(self::ACCOUNT_CLIENTS);
        $ventesAccount = $this->account(self::ACCOUNT_VENTES);
        $journal = Journal::where('type', 'ventes')->first();

        if (! $clientsAccount || ! $ventesAccount || ! $journal) {
            return null;
        }

        return $this->createEntry(
            journal: $journal,
            date: $order->created_at->toDateString(),
            description: 'Facture client : '.$order->order_number,
            lines: [
                ['account' => $clientsAccount, 'debit' => $order->total, 'label' => $order->customer_name],
                ['account' => $ventesAccount, 'credit' => $order->total, 'label' => $order->order_number],
            ],
            sourceType: 'order_invoice',
            sourceId: $order->id,
            reference: $order->order_number,
        );
    }

    public function postCustomerPayment(Order $order, float $amount, PaymentAccount $paymentAccount): ?JournalEntry
    {
        if ($this->alreadyPosted('order_payment', $order->id)) {
            return null;
        }

        $clientsAccount = $this->account(self::ACCOUNT_CLIENTS);
        $treasuryAccount = $paymentAccount->chartAccount;

        if (! $clientsAccount || ! $treasuryAccount || $amount <= 0) {
            return null;
        }

        $journal = $this->journalForPaymentAccount($paymentAccount);

        return $this->createEntry(
            journal: $journal,
            date: now()->toDateString(),
            description: 'Encaissement client : '.$order->order_number,
            lines: [
                ['account' => $treasuryAccount, 'debit' => $amount, 'label' => $order->customer_name],
                ['account' => $clientsAccount, 'credit' => $amount, 'label' => $order->order_number],
            ],
            sourceType: 'order_payment',
            sourceId: $order->id,
            reference: $order->order_number,
        );
    }

    public function postAssetAcquisition(FixedAsset $asset): ?JournalEntry
    {
        if ($this->alreadyPosted('asset_acquisition', $asset->id)) {
            return null;
        }

        if (! $asset->payment_account_id) {
            return null;
        }

        $immoAccount = $this->account(self::ACCOUNT_IMMOBILISATIONS);
        $treasuryAccount = $asset->paymentAccount?->chartAccount;

        if (! $immoAccount || ! $treasuryAccount) {
            return null;
        }

        $journal = $this->journalForPaymentAccount($asset->paymentAccount);

        return $this->createEntry(
            journal: $journal,
            date: $asset->acquisition_date,
            description: 'Acquisition immobilisation : '.$asset->name,
            lines: [
                ['account' => $immoAccount, 'debit' => $asset->acquisition_value, 'label' => $asset->name],
                ['account' => $treasuryAccount, 'credit' => $asset->acquisition_value, 'label' => $asset->paymentAccount->name],
            ],
            sourceType: 'asset_acquisition',
            sourceId: $asset->id,
            reference: 'IMMO-'.$asset->id,
        );
    }

    protected function journalForPaymentAccount(PaymentAccount $account): ?Journal
    {
        $type = $account->type === 'caisse' ? 'caisse' : 'banque';

        return Journal::where('type', $type)->first();
    }

    protected function account(string $code): ?ChartAccount
    {
        return ChartAccount::where('code', $code)->first();
    }

    protected function alreadyPosted(string $sourceType, int $sourceId): bool
    {
        return JournalEntry::where('source_type', $sourceType)->where('source_id', $sourceId)->exists();
    }

    protected function createEntry(?Journal $journal, $date, string $description, array $lines, ?string $sourceType = null, ?int $sourceId = null, ?string $reference = null): ?JournalEntry
    {
        if (! $journal) {
            return null;
        }

        $totalDebit = array_sum(array_column($lines, 'debit'));
        $totalCredit = array_sum(array_column($lines, 'credit'));

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            return null;
        }

        return DB::transaction(function () use ($journal, $date, $description, $lines, $sourceType, $sourceId, $reference) {
            $entry = JournalEntry::create([
                'journal_id' => $journal->id,
                'entry_date' => $date,
                'reference' => $reference,
                'description' => $description,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'created_by' => Auth::id(),
            ]);

            foreach ($lines as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_account_id' => $line['account']->id,
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'label' => $line['label'] ?? null,
                ]);
            }

            return $entry;
        });
    }
}
