<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartAccount;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::orderBy('name')->get()->map(fn (ExpenseCategory $c) => [
            'id' => $c->id,
            'name' => $c->name,
            'chart_account_id' => $c->chart_account_id,
            'is_active' => $c->is_active,
        ]);
        $chartAccounts = ChartAccount::where('class', 6)->orderBy('code')->get()->map(fn (ChartAccount $a) => [
            'id' => $a->id,
            'label' => $a->code.' — '.$a->name,
        ]);

        return Inertia::render('Admin/Finance/ExpenseCategories', [
            'categories' => $categories,
            'chartAccounts' => $chartAccounts,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:expense_categories,name'],
            'chart_account_id' => ['nullable', 'exists:chart_of_accounts,id'],
        ]);

        ExpenseCategory::create($data);

        return back()->with('success', 'Catégorie créée.');
    }

    public function update(Request $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:expense_categories,name,'.$expenseCategory->id],
            'chart_account_id' => ['nullable', 'exists:chart_of_accounts,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $expenseCategory->update($data);

        return back()->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        $expenseCategory->delete();

        return back()->with('success', 'Catégorie supprimée.');
    }
}
