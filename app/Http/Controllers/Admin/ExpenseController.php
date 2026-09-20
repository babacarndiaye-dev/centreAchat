<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentAccount;
use App\Models\PaymentAccountTransaction;
use App\Services\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'account']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('category')) {
            $query->where('expense_category_id', $request->integer('category'));
        }

        $expenses = $query->latest('expense_date')->paginate(20)->withQueryString()->through(fn (Expense $expense) => [
            'id' => $expense->id,
            'expense_date' => $expense->expense_date->format('d/m/Y'),
            'category_name' => $expense->category->name,
            'beneficiary' => $expense->beneficiary,
            'amount' => (float) $expense->amount,
            'status' => $expense->status,
            'status_label' => Expense::STATUSES[$expense->status],
            'status_badge_class' => $expense->statusBadgeClass(),
            'receipt_url' => $expense->receipt_path ? asset('fichiers/'.$expense->receipt_path) : null,
        ]);
        $categories = ExpenseCategory::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Finance/Expenses/Index', [
            'expenses' => $expenses,
            'categories' => $categories,
            'statuses' => Expense::STATUSES,
            'filters' => $request->only('status', 'category'),
        ]);
    }

    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $accounts = PaymentAccount::where('is_active', true)->orderBy('name')->get()->map(fn (PaymentAccount $a) => [
            'id' => $a->id,
            'name' => $a->name,
            'type_label' => PaymentAccount::TYPES[$a->type],
        ]);

        return Inertia::render('Admin/Finance/Expenses/Create', [
            'categories' => $categories,
            'accounts' => $accounts,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'payment_account_id' => ['nullable', 'exists:payment_accounts,id'],
            'expense_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'beneficiary' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'receipt' => ['nullable', 'file', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }
        unset($data['receipt']);

        $data['created_by'] = Auth::id();

        Expense::create($data);

        return redirect()->route('admin.depenses.index')->with('success', 'Dépense enregistrée, en attente de validation.');
    }

    public function validateExpense(Expense $depense, AccountingService $accounting): RedirectResponse
    {
        if ($depense->status !== 'en_attente') {
            return back()->with('error', 'Cette dépense a déjà été traitée.');
        }

        $depense->update([
            'status' => 'validee',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        if ($depense->payment_account_id) {
            PaymentAccountTransaction::create([
                'payment_account_id' => $depense->payment_account_id,
                'type' => 'sortie',
                'amount' => $depense->amount,
                'category' => $depense->category->name,
                'description' => 'Dépense : '.($depense->description ?: $depense->category->name),
                'reference' => 'DEP-'.$depense->id,
                'transaction_date' => $depense->expense_date,
                'created_by' => Auth::id(),
            ]);

            $accounting->postExpense($depense);
        }

        return back()->with('success', 'Dépense validée.');
    }

    public function reject(Expense $depense): RedirectResponse
    {
        $depense->update([
            'status' => 'rejetee',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        return back()->with('success', 'Dépense rejetée.');
    }

    public function destroy(Expense $depense): RedirectResponse
    {
        if ($depense->receipt_path) {
            Storage::disk('public')->delete($depense->receipt_path);
        }

        $depense->delete();

        return back()->with('success', 'Dépense supprimée.');
    }
}
