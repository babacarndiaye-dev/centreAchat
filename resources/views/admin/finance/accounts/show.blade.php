@extends('layouts.admin')

@section('title', $account->name)

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <span class="text-xs font-semibold uppercase tracking-wide text-terroir-terracotta">{{ \App\Models\PaymentAccount::TYPES[$account->type] }}</span>
        <h2 class="mt-1 font-display text-2xl font-semibold">{{ $account->name }}</h2>
    </div>
    <div class="flex items-center gap-3">
        <span class="text-2xl font-bold text-terroir-green">{{ number_format($account->balance(), 0, ',', ' ') }} FCFA</span>
        <a href="{{ route('admin.comptes-paiement.edit', $account) }}" class="btn-outline">Modifier</a>
    </div>
</div>

<div class="admin-card mt-6">
    <h3 class="font-display text-base font-semibold">Ajouter un mouvement</h3>
    <form action="{{ route('admin.comptes-paiement.mouvement', $account) }}" method="POST" class="mt-3 flex flex-wrap gap-3">
        @csrf
        <select name="type" required class="input w-32">
            <option value="entree">Entrée</option>
            <option value="sortie">Sortie</option>
        </select>
        <input type="number" step="0.01" name="amount" placeholder="Montant" required class="input w-32">
        <input type="date" name="transaction_date" value="{{ now()->format('Y-m-d') }}" required class="input w-40">
        <input type="text" name="category" placeholder="Catégorie" class="input w-40">
        <input type="text" name="description" placeholder="Description" required class="input min-w-[160px] flex-1">
        <button type="submit" class="btn-primary">Ajouter</button>
    </form>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Date</th>
                <th>Description</th>
                <th>Catégorie</th>
                <th class="pr-6 text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($account->transactions as $transaction)
                <tr>
                    <td class="pl-6 text-terroir-dark/60">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                    <td>{{ $transaction->description }}{{ $transaction->reference ? ' ('.$transaction->reference.')' : '' }}</td>
                    <td class="text-terroir-dark/60">{{ $transaction->category ?? '—' }}</td>
                    <td class="pr-6 text-right font-semibold {{ $transaction->type === 'entree' ? 'text-terroir-green' : 'text-terroir-terracotta' }}">
                        {{ $transaction->type === 'entree' ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-8 text-center text-terroir-dark/40">Aucun mouvement.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
