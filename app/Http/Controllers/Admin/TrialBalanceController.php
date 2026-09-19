<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartAccount;

class TrialBalanceController extends Controller
{
    public function index()
    {
        $accounts = ChartAccount::with('lines')
            ->orderBy('code')
            ->get()
            ->map(function ($account) {
                $account->debit_total = (float) $account->lines->sum('debit');
                $account->credit_total = (float) $account->lines->sum('credit');
                $account->solde = $account->debit_total - $account->credit_total;

                return $account;
            })
            ->filter(fn ($account) => $account->debit_total > 0 || $account->credit_total > 0);

        $totalDebit = $accounts->sum('debit_total');
        $totalCredit = $accounts->sum('credit_total');

        return view('admin.accounting.balance.index', compact('accounts', 'totalDebit', 'totalCredit'));
    }
}
