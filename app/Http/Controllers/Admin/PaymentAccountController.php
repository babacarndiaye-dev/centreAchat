<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentAccount;
use App\Models\PaymentAccountTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentAccountController extends Controller
{
    public function index()
    {
        $accounts = PaymentAccount::orderBy('type')->orderBy('name')->get();

        return view('admin.finance.accounts.index', compact('accounts'));
    }

    public function create()
    {
        $chartAccounts = \App\Models\ChartAccount::where('class', 5)->orderBy('code')->get();

        return view('admin.finance.accounts.create', compact('chartAccounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        PaymentAccount::create($this->validated($request));

        return redirect()->route('admin.comptes-paiement.index')->with('success', 'Compte créé.');
    }

    public function edit(PaymentAccount $compte)
    {
        $chartAccounts = \App\Models\ChartAccount::where('class', 5)->orderBy('code')->get();

        return view('admin.finance.accounts.edit', ['account' => $compte, 'chartAccounts' => $chartAccounts]);
    }

    public function update(Request $request, PaymentAccount $compte): RedirectResponse
    {
        $compte->update($this->validated($request));

        return redirect()->route('admin.comptes-paiement.index')->with('success', 'Compte mis à jour.');
    }

    public function destroy(PaymentAccount $compte): RedirectResponse
    {
        $compte->delete();

        return back()->with('success', 'Compte supprimé.');
    }

    public function show(PaymentAccount $compte)
    {
        $compte->load(['transactions' => fn ($q) => $q->latest('transaction_date')->latest()]);

        return view('admin.finance.accounts.show', ['account' => $compte]);
    }

    public function addTransaction(Request $request, PaymentAccount $compte): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:entree,sortie'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
        ]);

        PaymentAccountTransaction::create([
            ...$data,
            'payment_account_id' => $compte->id,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Mouvement enregistré.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:banque,mobile_money,caisse'],
            'chart_account_id' => ['nullable', 'exists:chart_of_accounts,id'],
            'provider' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'initial_balance' => ['required', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
