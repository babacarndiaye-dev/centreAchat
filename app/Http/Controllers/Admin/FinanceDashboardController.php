<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Order;
use App\Models\PaymentAccount;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Inertia\Inertia;

class FinanceDashboardController extends Controller
{
    public function index()
    {
        $accounts = PaymentAccount::where('is_active', true)->get();

        $balancesByType = collect(PaymentAccount::TYPES)->mapWithKeys(function ($label, $type) use ($accounts) {
            return [$type => $accounts->where('type', $type)->sum(fn ($account) => $account->balance())];
        });

        $treasuryAvailable = $balancesByType->sum();

        $receivables = Order::whereIn('payment_status', ['en_attente', 'partiellement_paye'])
            ->whereNotIn('status', ['annulee', 'remboursee', 'brouillon'])
            ->get()
            ->sum(fn ($order) => $order->total - $order->amountPaid());

        $payables = Supplier::all()->sum(fn ($supplier) => $supplier->balance());

        $monthRevenue = Order::whereNotIn('status', ['annulee', 'remboursee', 'brouillon'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $monthPurchases = PurchaseOrder::whereNotIn('status', ['annulee', 'brouillon'])
            ->whereMonth('order_date', now()->month)
            ->whereYear('order_date', now()->year)
            ->sum('total');

        $monthExpenses = Expense::where('status', 'validee')
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $provisionalResult = $monthRevenue - $monthPurchases - $monthExpenses;

        $pendingExpenses = Expense::where('status', 'en_attente')->count();

        $recentExpenses = Expense::with('category')->where('status', 'validee')->latest('expense_date')->take(6)->get();

        return Inertia::render('Admin/Finance/Dashboard', [
            'balancesByType' => $balancesByType,
            'types' => PaymentAccount::TYPES,
            'treasuryAvailable' => (float) $treasuryAvailable,
            'receivables' => (float) $receivables,
            'payables' => (float) $payables,
            'monthRevenue' => (float) $monthRevenue,
            'monthPurchases' => (float) $monthPurchases,
            'monthExpenses' => (float) $monthExpenses,
            'provisionalResult' => (float) $provisionalResult,
            'pendingExpenses' => $pendingExpenses,
            'recentExpenses' => $recentExpenses->map(fn (Expense $expense) => [
                'id' => $expense->id,
                'category_name' => $expense->category->name,
                'expense_date' => $expense->expense_date->format('d/m/Y'),
                'amount' => (float) $expense->amount,
            ]),
        ]);
    }
}
