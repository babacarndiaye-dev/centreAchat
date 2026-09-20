<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartAccount;
use App\Models\PaymentAccount;
use App\Models\PaymentAccountTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PaymentAccountController extends Controller
{
    public function index()
    {
        $accounts = PaymentAccount::orderBy('type')->orderBy('name')->get()->map(fn (PaymentAccount $account) => [
            'id' => $account->id,
            'name' => $account->name,
            'type' => $account->type,
            'type_label' => PaymentAccount::TYPES[$account->type],
            'provider' => $account->provider,
            'account_number' => $account->account_number,
            'balance' => (float) $account->balance(),
            'is_active' => $account->is_active,
        ]);

        return Inertia::render('Admin/Finance/Accounts/Index', [
            'accounts' => $accounts,
        ]);
    }

    protected function chartAccountOptions()
    {
        return ChartAccount::where('class', 5)->orderBy('code')->get()->map(fn (ChartAccount $a) => [
            'id' => $a->id,
            'label' => $a->code.' — '.$a->name,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Finance/Accounts/Form', [
            'types' => PaymentAccount::TYPES,
            'chartAccounts' => $this->chartAccountOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        PaymentAccount::create($this->validated($request));

        return redirect()->route('admin.comptes-paiement.index')->with('success', 'Compte créé.');
    }

    public function edit(PaymentAccount $compte)
    {
        return Inertia::render('Admin/Finance/Accounts/Form', [
            'types' => PaymentAccount::TYPES,
            'chartAccounts' => $this->chartAccountOptions(),
            'account' => [
                'id' => $compte->id,
                'name' => $compte->name,
                'type' => $compte->type,
                'provider' => $compte->provider,
                'account_number' => $compte->account_number,
                'chart_account_id' => $compte->chart_account_id,
                'initial_balance' => (float) $compte->initial_balance,
                'notes' => $compte->notes,
                'is_active' => $compte->is_active,
            ],
        ]);
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

        return Inertia::render('Admin/Finance/Accounts/Show', [
            'account' => [
                'id' => $compte->id,
                'name' => $compte->name,
                'type' => $compte->type,
                'type_label' => PaymentAccount::TYPES[$compte->type],
                'balance' => (float) $compte->balance(),
                'transactions' => $compte->transactions->map(fn (PaymentAccountTransaction $t) => [
                    'id' => $t->id,
                    'transaction_date' => $t->transaction_date->format('d/m/Y'),
                    'description' => $t->description,
                    'reference' => $t->reference,
                    'category' => $t->category,
                    'type' => $t->type,
                    'amount' => (float) $t->amount,
                ]),
            ],
        ]);
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
