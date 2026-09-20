@extends('layouts.admin')

@section('title', 'Comptes de paiement')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-terroir-dark/50">Banque, Mobile Money, Caisse.</p>
    <a href="{{ route('admin.comptes-paiement.create') }}" class="btn-primary">
        <span class="material-symbols-outlined text-lg">add</span>
        Nouveau compte
    </a>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($accounts as $account)
        <a href="{{ route('admin.comptes-paiement.show', $account) }}" class="admin-card block transition hover:-translate-y-0.5 hover:shadow-lg">
            <span class="text-xs font-semibold uppercase tracking-wide text-terroir-terracotta">{{ \App\Models\PaymentAccount::TYPES[$account->type] }}</span>
            <h3 class="mt-1.5 font-display text-lg font-semibold">{{ $account->name }}</h3>
            @if($account->provider)
                <p class="mt-1 text-sm text-terroir-dark/50">{{ $account->provider }} {{ $account->account_number ? '— '.$account->account_number : '' }}</p>
            @endif
            <p class="mt-1.5 text-2xl font-bold text-terroir-green">{{ number_format($account->balance(), 0, ',', ' ') }} FCFA</p>
            @unless($account->is_active)
                <span class="admin-badge-neutral mt-1.5">Inactif</span>
            @endunless
        </a>
    @empty
        <p class="text-terroir-dark/50">Aucun compte de paiement.</p>
    @endforelse
</div>
@endsection
