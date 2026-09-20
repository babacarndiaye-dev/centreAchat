@extends('layouts.admin')

@section('title', 'Trésorerie')

@section('content')
<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
    <div class="admin-card">
        <p class="text-sm text-terroir-dark/50">Trésorerie disponible</p>
        <p class="mt-1.5 text-2xl font-bold text-terroir-green">{{ number_format($treasuryAvailable, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="admin-card">
        <p class="text-sm text-terroir-dark/50">Créances clients</p>
        <p class="mt-1.5 text-2xl font-bold text-terroir-terracotta">{{ number_format($receivables, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="admin-card">
        <p class="text-sm text-terroir-dark/50">Dettes fournisseurs</p>
        <p class="mt-1.5 text-2xl font-bold text-terroir-terracotta">{{ number_format($payables, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="admin-card">
        <p class="text-sm text-terroir-dark/50">Dépenses en attente</p>
        <p class="mt-1.5 text-2xl font-bold">{{ $pendingExpenses }}</p>
    </div>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="admin-card">
        <h2 class="font-display text-base font-semibold">Solde par type de compte</h2>
        <div class="mt-3 flex flex-col gap-3">
            @foreach(\App\Models\PaymentAccount::TYPES as $type => $label)
                <div class="flex items-center justify-between rounded-lg bg-terroir-cream px-4 py-3 text-sm">
                    <span>{{ $label }}</span>
                    <span class="font-semibold">{{ number_format($balancesByType[$type], 0, ',', ' ') }} FCFA</span>
                </div>
            @endforeach
        </div>
        <a href="{{ route('admin.comptes-paiement.index') }}" class="mt-3 block text-sm font-semibold text-terroir-green">Gérer les comptes →</a>
    </div>

    <div class="admin-card">
        <h2 class="font-display text-base font-semibold">Résultat provisoire du mois</h2>
        <dl class="mt-3 text-sm">
            <div class="mb-2 flex justify-between"><dt class="text-terroir-dark/50">Chiffre d'affaires (ventes)</dt><dd class="font-medium text-terroir-green">+{{ number_format($monthRevenue, 0, ',', ' ') }} FCFA</dd></div>
            <div class="mb-2 flex justify-between"><dt class="text-terroir-dark/50">Achats fournisseurs</dt><dd class="font-medium text-terroir-terracotta">-{{ number_format($monthPurchases, 0, ',', ' ') }} FCFA</dd></div>
            <div class="mb-2 flex justify-between"><dt class="text-terroir-dark/50">Dépenses validées</dt><dd class="font-medium text-terroir-terracotta">-{{ number_format($monthExpenses, 0, ',', ' ') }} FCFA</dd></div>
            <div class="flex justify-between border-t border-terroir-green/10 pt-2 text-base font-bold {{ $provisionalResult >= 0 ? 'text-terroir-green' : 'text-terroir-terracotta' }}">
                <dt>Résultat provisoire</dt><dd>{{ number_format($provisionalResult, 0, ',', ' ') }} FCFA</dd>
            </div>
        </dl>
        <p class="mt-3 text-sm text-terroir-dark/40">Estimation en trésorerie (encaissements/décaissements), hors amortissements et charges non décaissées.</p>
    </div>
</div>

<div class="admin-card mt-6">
    <div class="flex items-center justify-between">
        <h2 class="font-display text-base font-semibold">Dépenses récentes validées</h2>
        <a href="{{ route('admin.depenses.index') }}" class="admin-link">Voir tout →</a>
    </div>
    <table class="admin-table mt-3">
        <thead>
            <tr>
                <th>Catégorie</th>
                <th>Date</th>
                <th class="text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentExpenses as $expense)
                <tr>
                    <td>{{ $expense->category->name }}</td>
                    <td class="text-terroir-dark/60">{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td class="text-right font-semibold">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</td>
                </tr>
            @empty
                <tr><td colspan="3" class="py-6 text-center text-terroir-dark/40">Aucune dépense validée.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
