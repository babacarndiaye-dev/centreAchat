<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankStatementImport;
use App\Models\BankStatementLine;
use App\Models\PaymentAccount;
use App\Models\PaymentAccountTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankReconciliationController extends Controller
{
    public function index(Request $request)
    {
        $accounts = PaymentAccount::whereIn('type', ['banque', 'mobile_money'])->orderBy('name')->get();
        $account = null;
        $unmatchedLines = collect();
        $unreconciledTransactions = collect();
        $reconciledCount = 0;
        $stats = null;

        if ($request->filled('compte')) {
            $account = PaymentAccount::find($request->integer('compte'));

            if ($account) {
                $unmatchedLines = $account->statementLines()
                    ->whereIn('status', ['non_rapproche', 'ecart'])
                    ->orderBy('statement_date')
                    ->get();

                $unreconciledTransactions = $account->transactions()
                    ->where('is_reconciled', false)
                    ->orderBy('transaction_date')
                    ->get();

                $reconciledCount = $account->statementLines()->where('status', 'rapproche')->count();

                $stats = [
                    'solde_comptable' => $account->balance(),
                    'total_releve' => (float) $account->statementLines()->sum('amount'),
                    'lignes_importees' => $account->statementLines()->count(),
                    'lignes_en_attente' => $unmatchedLines->count(),
                ];
            }
        }

        return view('admin.accounting.reconciliation.index', compact(
            'accounts', 'account', 'unmatchedLines', 'unreconciledTransactions', 'reconciledCount', 'stats'
        ));
    }

    public function import(Request $request, PaymentAccount $compte): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $rows = [];
        $isFirstRow = true;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) < 3) {
                $row = fgetcsv($handle, 0, ',') ?: $row;
            }

            if ($isFirstRow && ! is_numeric(str_replace(',', '.', $row[2] ?? ''))) {
                $isFirstRow = false;

                continue;
            }

            if (count($row) >= 3) {
                $rows[] = $row;
            }

            $isFirstRow = false;
        }

        fclose($handle);

        if (empty($rows)) {
            return back()->with('error', 'Aucune ligne valide trouvée dans le fichier. Format attendu : date;description;montant');
        }

        $import = DB::transaction(function () use ($rows, $compte, $request) {
            $import = BankStatementImport::create([
                'payment_account_id' => $compte->id,
                'original_filename' => $request->file('file')->getClientOriginalName(),
                'lines_count' => count($rows),
                'created_by' => Auth::id(),
            ]);

            foreach ($rows as $row) {
                [$date, $description, $amount] = [$row[0], $row[1], $row[2]];

                $parsedDate = \Illuminate\Support\Str::contains($date, '/')
                    ? \Carbon\Carbon::createFromFormat('d/m/Y', trim($date))
                    : \Carbon\Carbon::parse(trim($date));

                $line = BankStatementLine::create([
                    'bank_statement_import_id' => $import->id,
                    'payment_account_id' => $compte->id,
                    'statement_date' => $parsedDate,
                    'description' => trim($description),
                    'amount' => (float) str_replace(',', '.', $amount),
                    'reference' => trim($row[3] ?? '') ?: null,
                    'status' => 'non_rapproche',
                ]);

                $this->tryAutoMatch($line, $compte);
            }

            return $import;
        });

        $matched = $import->lines()->where('status', 'rapproche')->count();

        return back()->with('success', "{$import->lines_count} ligne(s) importée(s), {$matched} rapprochée(s) automatiquement.");
    }

    protected function tryAutoMatch(BankStatementLine $line, PaymentAccount $account): void
    {
        $candidates = $account->transactions()
            ->where('is_reconciled', false)
            ->whereBetween('transaction_date', [$line->statement_date->copy()->subDays(5), $line->statement_date->copy()->addDays(5)])
            ->get()
            ->filter(fn (PaymentAccountTransaction $t) => abs($t->signedAmount() - (float) $line->amount) < 0.01);

        $match = $candidates->sortBy(fn ($t) => abs($t->transaction_date->diffInDays($line->statement_date)))->first();

        if ($match) {
            $line->update(['matched_transaction_id' => $match->id, 'status' => 'rapproche']);
            $match->update(['is_reconciled' => true, 'reconciled_at' => now()]);
        }
    }

    public function match(Request $request, BankStatementLine $ligne): RedirectResponse
    {
        $data = $request->validate([
            'transaction_id' => ['required', 'exists:payment_account_transactions,id'],
        ]);

        $transaction = PaymentAccountTransaction::findOrFail($data['transaction_id']);

        $ligne->update(['matched_transaction_id' => $transaction->id, 'status' => 'rapproche']);
        $transaction->update(['is_reconciled' => true, 'reconciled_at' => now()]);

        return back()->with('success', 'Ligne rapprochée.');
    }

    public function unmatch(BankStatementLine $ligne): RedirectResponse
    {
        if ($ligne->matched_transaction_id) {
            PaymentAccountTransaction::where('id', $ligne->matched_transaction_id)->update(['is_reconciled' => false, 'reconciled_at' => null]);
        }

        $ligne->update(['matched_transaction_id' => null, 'status' => 'non_rapproche']);

        return back()->with('success', 'Rapprochement annulé.');
    }

    public function markDiscrepancy(BankStatementLine $ligne): RedirectResponse
    {
        $ligne->update(['status' => 'ecart']);

        return back()->with('success', 'Ligne marquée comme écart à investiguer.');
    }

    public function createTransaction(BankStatementLine $ligne): RedirectResponse
    {
        $transaction = PaymentAccountTransaction::create([
            'payment_account_id' => $ligne->payment_account_id,
            'type' => $ligne->amount >= 0 ? 'entree' : 'sortie',
            'amount' => abs($ligne->amount),
            'category' => 'Relevé bancaire',
            'description' => $ligne->description,
            'reference' => $ligne->reference,
            'transaction_date' => $ligne->statement_date,
            'is_reconciled' => true,
            'reconciled_at' => now(),
            'created_by' => Auth::id(),
        ]);

        $ligne->update(['matched_transaction_id' => $transaction->id, 'status' => 'rapproche']);

        return back()->with('success', 'Mouvement créé et rapproché à partir du relevé.');
    }
}
