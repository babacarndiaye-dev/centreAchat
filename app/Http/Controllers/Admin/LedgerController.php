<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartAccount;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $accounts = ChartAccount::orderBy('code')->get();
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
                    $line->running_balance = $runningBalance;

                    return $line;
                });
            }
        }

        return view('admin.accounting.ledger.index', compact('accounts', 'account', 'lines'));
    }
}
