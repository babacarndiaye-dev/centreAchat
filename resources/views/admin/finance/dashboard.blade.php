@extends('layouts.admin')

@section('title', 'Trésorerie')

@section('content')
<div class="uk-grid-small uk-child-width-1-2@s uk-child-width-1-4@l" uk-grid>
    <div>
        <div class="uk-card uk-card-default" style="padding:24px;">
            <p class="uk-text-small uk-text-muted" style="margin:0;">Trésorerie disponible</p>
            <p class="uk-margin-small-top" style="font-size:1.5rem; font-weight:700; color:#1D8A4E; margin-bottom:0;">{{ number_format($treasuryAvailable, 0, ',', ' ') }} FCFA</p>
        </div>
    </div>
    <div>
        <div class="uk-card uk-card-default" style="padding:24px;">
            <p class="uk-text-small uk-text-muted" style="margin:0;">Créances clients</p>
            <p class="uk-margin-small-top" style="font-size:1.5rem; font-weight:700; color:#E8604F; margin-bottom:0;">{{ number_format($receivables, 0, ',', ' ') }} FCFA</p>
        </div>
    </div>
    <div>
        <div class="uk-card uk-card-default" style="padding:24px;">
            <p class="uk-text-small uk-text-muted" style="margin:0;">Dettes fournisseurs</p>
            <p class="uk-margin-small-top" style="font-size:1.5rem; font-weight:700; color:#E8604F; margin-bottom:0;">{{ number_format($payables, 0, ',', ' ') }} FCFA</p>
        </div>
    </div>
    <div>
        <div class="uk-card uk-card-default" style="padding:24px;">
            <p class="uk-text-small uk-text-muted" style="margin:0;">Dépenses en attente</p>
            <p class="uk-margin-small-top" style="font-size:1.5rem; font-weight:700; margin-bottom:0;">{{ $pendingExpenses }}</p>
        </div>
    </div>
</div>

<div class="uk-grid-small uk-child-width-1-2@l uk-margin-top" uk-grid>
    <div>
        <div class="uk-card uk-card-default" style="padding:24px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem; margin:0;">Solde par type de compte</h2>
            <div class="uk-margin-small-top" style="display:flex; flex-direction:column; gap:12px;">
                @foreach(\App\Models\PaymentAccount::TYPES as $type => $label)
                    <div class="uk-flex uk-flex-between uk-flex-middle" style="background:#F7F8F5; border-radius:8px; padding:12px 16px; font-size:.875rem;">
                        <span>{{ $label }}</span>
                        <span style="font-weight:600;">{{ number_format($balancesByType[$type], 0, ',', ' ') }} FCFA</span>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('admin.comptes-paiement.index') }}" class="uk-display-block uk-margin-small-top uk-text-small" style="font-weight:600; color:#1D8A4E;">Gérer les comptes →</a>
        </div>
    </div>

    <div>
        <div class="uk-card uk-card-default" style="padding:24px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem; margin:0;">Résultat provisoire du mois</h2>
            <dl class="uk-margin-small-top" style="font-size:.875rem;">
                <div class="uk-flex uk-flex-between" style="margin-bottom:8px;"><dt class="uk-text-muted">Chiffre d'affaires (ventes)</dt><dd style="font-weight:500; color:#1D8A4E; margin:0;">+{{ number_format($monthRevenue, 0, ',', ' ') }} FCFA</dd></div>
                <div class="uk-flex uk-flex-between" style="margin-bottom:8px;"><dt class="uk-text-muted">Achats fournisseurs</dt><dd style="font-weight:500; color:#E8604F; margin:0;">-{{ number_format($monthPurchases, 0, ',', ' ') }} FCFA</dd></div>
                <div class="uk-flex uk-flex-between" style="margin-bottom:8px;"><dt class="uk-text-muted">Dépenses validées</dt><dd style="font-weight:500; color:#E8604F; margin:0;">-{{ number_format($monthExpenses, 0, ',', ' ') }} FCFA</dd></div>
                <div class="uk-flex uk-flex-between" style="border-top:1px solid rgba(29,138,78,.1); padding-top:8px; font-size:1rem; font-weight:700; color:{{ $provisionalResult >= 0 ? '#1D8A4E' : '#E8604F' }};">
                    <dt>Résultat provisoire</dt><dd style="margin:0;">{{ number_format($provisionalResult, 0, ',', ' ') }} FCFA</dd>
                </div>
            </dl>
            <p class="uk-margin-small-top uk-text-small" style="color:rgba(31,35,40,.4); margin-bottom:0;">Estimation en trésorerie (encaissements/décaissements), hors amortissements et charges non décaissées.</p>
        </div>
    </div>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="padding:24px;">
    <div class="uk-flex uk-flex-between uk-flex-middle">
        <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem; margin:0;">Dépenses récentes validées</h2>
        <a href="{{ route('admin.depenses.index') }}" class="uk-text-small" style="font-weight:600; color:#1D8A4E;">Voir tout →</a>
    </div>
    <table class="uk-table uk-table-divider uk-table-middle uk-margin-small-top">
        <thead>
            <tr>
                <th>Catégorie</th>
                <th>Date</th>
                <th class="uk-text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentExpenses as $expense)
                <tr>
                    <td>{{ $expense->category->name }}</td>
                    <td class="uk-text-muted">{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td class="uk-text-right" style="font-weight:600;">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</td>
                </tr>
            @empty
                <tr><td colspan="3" class="uk-text-center uk-text-muted" style="padding:24px 0;">Aucune dépense validée.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
