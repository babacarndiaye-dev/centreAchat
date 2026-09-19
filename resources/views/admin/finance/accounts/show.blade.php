@extends('layouts.admin')

@section('title', $account->name)

@section('content')
<div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap" style="gap:16px;">
    <div>
        <span class="uk-text-small" style="font-weight:600; text-transform:uppercase; letter-spacing:.03em; color:#E8604F;">{{ \App\Models\PaymentAccount::TYPES[$account->type] }}</span>
        <h2 class="uk-margin-remove" style="font-family:'Fraunces',serif; font-weight:600; font-size:1.5rem; margin-top:4px;">{{ $account->name }}</h2>
    </div>
    <div class="uk-flex uk-flex-middle" style="gap:12px;">
        <span style="font-size:1.5rem; font-weight:700; color:#1D8A4E;">{{ number_format($account->balance(), 0, ',', ' ') }} FCFA</span>
        <a href="{{ route('admin.comptes-paiement.edit', $account) }}" class="uk-button uk-button-default">Modifier</a>
    </div>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="padding:24px;">
    <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem; margin:0;">Ajouter un mouvement</h3>
    <form action="{{ route('admin.comptes-paiement.mouvement', $account) }}" method="POST" class="uk-grid-small uk-child-width-1-6@s uk-margin-small-top" uk-grid>
        @csrf
        <div>
            <select name="type" required class="uk-select">
                <option value="entree">Entrée</option>
                <option value="sortie">Sortie</option>
            </select>
        </div>
        <div>
            <input type="number" step="0.01" name="amount" placeholder="Montant" required class="uk-input">
        </div>
        <div>
            <input type="date" name="transaction_date" value="{{ now()->format('Y-m-d') }}" required class="uk-input">
        </div>
        <div>
            <input type="text" name="category" placeholder="Catégorie" class="uk-input">
        </div>
        <div>
            <input type="text" name="description" placeholder="Description" required class="uk-input">
        </div>
        <div>
            <button type="submit" class="uk-button uk-button-primary">Ajouter</button>
        </div>
    </form>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Catégorie</th>
                <th class="uk-text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($account->transactions as $transaction)
                <tr>
                    <td class="uk-text-muted">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                    <td>{{ $transaction->description }}{{ $transaction->reference ? ' ('.$transaction->reference.')' : '' }}</td>
                    <td class="uk-text-muted">{{ $transaction->category ?? '—' }}</td>
                    <td class="uk-text-right" style="font-weight:600; color:{{ $transaction->type === 'entree' ? '#1D8A4E' : '#E8604F' }};">
                        {{ $transaction->type === 'entree' ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucun mouvement.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
