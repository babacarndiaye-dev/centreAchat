@extends('layouts.admin')

@section('title', 'Comptes de paiement')

@section('content')
<div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap" style="gap:12px;">
    <p class="uk-text-small uk-text-muted" style="margin:0;">Banque, Mobile Money, Caisse.</p>
    <a href="{{ route('admin.comptes-paiement.create') }}" class="uk-button uk-button-primary">+ Nouveau compte</a>
</div>

<div class="uk-grid-small uk-child-width-1-2@s uk-child-width-1-3@l uk-margin-top" uk-grid>
    @forelse($accounts as $account)
        <div>
            <a href="{{ route('admin.comptes-paiement.show', $account) }}" class="uk-card uk-card-default uk-display-block" style="padding:24px; transition:transform .15s;">
                <span class="uk-text-small" style="font-weight:600; text-transform:uppercase; letter-spacing:.03em; color:#E8604F;">{{ \App\Models\PaymentAccount::TYPES[$account->type] }}</span>
                <h3 class="uk-margin-small-top" style="font-family:'Fraunces',serif; font-weight:600; font-size:1.1rem; margin-bottom:0;">{{ $account->name }}</h3>
                @if($account->provider)
                    <p class="uk-text-small uk-text-muted" style="margin:4px 0 0;">{{ $account->provider }} {{ $account->account_number ? '— '.$account->account_number : '' }}</p>
                @endif
                <p class="uk-margin-small-top" style="font-size:1.5rem; font-weight:700; color:#1D8A4E; margin-bottom:0;">{{ number_format($account->balance(), 0, ',', ' ') }} FCFA</p>
                @unless($account->is_active)
                    <span class="uk-label uk-margin-small-top" style="background:rgba(31,35,40,.08); color:rgba(31,35,40,.6);">Inactif</span>
                @endunless
            </a>
        </div>
    @empty
        <p class="uk-text-muted">Aucun compte de paiement.</p>
    @endforelse
</div>
@endsection
