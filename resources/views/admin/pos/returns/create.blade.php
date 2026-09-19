@extends('layouts.admin')

@section('title', 'Nouveau retour')

@section('content')
<div class="uk-card uk-card-default" style="max-width:42rem; padding:32px;">
    <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Rechercher une commande</h2>
    <form method="GET" class="uk-margin-top uk-flex" style="gap:8px;">
        <input type="text" name="order_number" value="{{ request('order_number') }}" placeholder="Numéro de commande (ex : CA-20260818-XXXXXX)" required class="uk-input" style="flex:1;">
        <button type="submit" class="uk-button uk-button-default">Rechercher</button>
    </form>
    @error('order_number') <p class="uk-text-small uk-margin-small-top" style="color:#E8604F;">{{ $message }}</p> @enderror
</div>

@if($order)
    <div class="uk-card uk-card-default uk-margin-top" style="max-width:42rem; padding:32px;">
        <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">{{ $order->order_number }}</h2>
        <p class="uk-text-small uk-text-muted">{{ $order->customer_name }} — {{ $order->created_at->format('d/m/Y') }}</p>

        <form action="{{ route('admin.pos.retours.store') }}" method="POST" class="uk-margin-top">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">

            <div style="display:flex; flex-direction:column; gap:12px;">
                @foreach($order->items as $item)
                    @if($item->returnableQuantity() > 0)
                        <div class="uk-flex uk-flex-between uk-flex-middle" style="gap:16px; border-radius:8px; background:rgba(247,248,245,.6); padding:10px 16px;">
                            <div>
                                <p class="uk-text-small" style="font-weight:600;">{{ $item->product_name }}</p>
                                <p class="uk-text-small uk-text-muted">Acheté : {{ $item->quantity }} — Retournable : {{ $item->returnableQuantity() }}</p>
                            </div>
                            <input type="number" name="quantity[{{ $item->id }}]" min="0" max="{{ $item->returnableQuantity() }}" value="0" class="uk-input" style="width:6rem;">
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="uk-margin-top">
                <label class="uk-form-label" for="notes">Motif du retour</label>
                <textarea id="notes" name="notes" rows="2" class="uk-textarea"></textarea>
            </div>

            <button type="submit" class="uk-button uk-button-primary uk-margin-top">Valider le retour</button>
        </form>
    </div>
@endif
@endsection
