<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartAccount;
use Inertia\Inertia;

class TrialBalanceController extends Controller
{
    public function index()
    {
        $accounts = ChartAccount::with('lines')
            ->orderBy('code')
            ->get()
            ->map(function ($account) {
                $debitTotal = (float) $account->lines->sum('debit');
                $creditTotal = (float) $account->lines->sum('credit');

                return [
                    'id' => $account->id,
                    'code' => $account->code,
                    'name' => $account->name,
                    'debit_total' => $debitTotal,
                    'credit_total' => $creditTotal,
                    'solde' => $debitTotal - $creditTotal,
                ];
            })
            ->filter(fn ($account) => $account['debit_total'] > 0 || $account['credit_total'] > 0)
            ->values();

        $totalDebit = $accounts->sum('debit_total');
        $totalCredit = $accounts->sum('credit_total');

        return Inertia::render('Admin/Comptabilite/TrialBalance', [
            'accounts' => $accounts,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
        ]);
    }
}
