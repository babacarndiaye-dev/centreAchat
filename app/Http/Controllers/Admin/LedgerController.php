<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $accounts = ChartAccount::orderBy('code')->get(['id', 'code', 'name']);
        $account = null;
        $lines = collect();
        $runningBalance = 0;

        if ($request->filled('compte')) {
            $account = ChartAccount::find($request->integer('compte'));

            if ($account) {
                $query = $account->lines()->with('entry.journal')->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                    ->orderBy('journal_entries.entry_date')
                    ->orderBy('journal_entry_lines.id')
                    ->select('journal_entry_lines.*');

                $lines = $query->get()->map(function ($line) use (&$runningBalance) {
                    $runningBalance += $line->debit - $line->credit;

                    return [
                        'id' => $line->id,
                        'date' => $line->entry->entry_date->format('d/m/Y'),
                        'entry_id' => $line->entry->id,
                        'description' => $line->entry->description,
                        'label' => $line->label,
                        'debit' => (float) $line->debit,
                        'credit' => (float) $line->credit,
                        'running_balance' => (float) $runningBalance,
                    ];
                });
            }
        }

        return Inertia::render('Admin/Comptabilite/Ledger', [
            'accounts' => $accounts,
            'account' => $account ? ['id' => $account->id, 'code' => $account->code, 'name' => $account->name] : null,
            'lines' => $lines->values(),
            'selectedAccount' => $request->integer('compte') ?: null,
        ]);
    }
}
