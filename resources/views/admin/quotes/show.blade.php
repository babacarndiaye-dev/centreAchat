@extends('layouts.admin')

@section('title', $quote->quote_number)

@section('content')
<div class="uk-flex uk-flex-wrap uk-flex-between uk-flex-middle" style="gap:16px;">
    <div>
        <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.5rem;">{{ $quote->quote_number }}</h2>
        <p class="uk-text-small uk-text-muted">Demandé par {{ $quote->user->name }} ({{ $quote->user->company_name ?? $quote->user->email }})</p>
    </div>
    <span class="uk-label" style="background:#F7F8F5; color:#1D8A4E; padding:6px 16px; font-size:.875rem;">{{ \App\Models\Quote::STATUSES[$quote->status] }}</span>
</div>

@if($quote->notes)
    <div class="uk-card uk-card-default uk-margin-top uk-text-small" style="padding:16px;">{{ $quote->notes }}</div>
@endif

<div class="uk-card uk-card-default uk-margin-top" style="padding:24px;">
    <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Articles demandés</h3>

    @if(in_array($quote->status, ['en_attente', 'envoye']))
        <form action="{{ route('admin.devis.send', $quote) }}" method="POST" class="uk-margin-top">
            @csrf
            <table class="uk-table uk-table-divider">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th class="uk-text-right">Quantité</th>
                        <th class="uk-text-right">Prix unitaire proposé</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quote->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td class="uk-text-right">{{ $item->quantity }}</td>
                            <td class="uk-text-right">
                                <input type="number" step="0.01" name="unit_price[{{ $item->id }}]" value="{{ $item->unit_price ?? $item->product->professional_price ?? $item->product->price }}" required class="uk-input uk-text-right" style="width:8rem;">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="uk-margin-top" style="max-width:20rem;">
                <label class="uk-form-label" for="valid_until">Valable jusqu'au</label>
                <input type="date" id="valid_until" name="valid_until" value="{{ now()->addDays(7)->format('Y-m-d') }}" class="uk-input">
            </div>

            <button type="submit" class="uk-button uk-button-primary uk-margin-top">Envoyer le devis au client</button>
        </form>
    @else
        <table class="uk-table uk-table-divider uk-margin-small-top">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="uk-text-right">Quantité</th>
                    <th class="uk-text-right">Prix unitaire</th>
                    <th class="uk-text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="uk-text-right">{{ $item->quantity }}</td>
                        <td class="uk-text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                        <td class="uk-text-right" style="font-weight:600;">{{ number_format($item->total(), 0, ',', ' ') }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="uk-margin-top uk-text-right" style="font-size:1rem; font-weight:700; color:#1D8A4E;">Total : {{ number_format($quote->total, 0, ',', ' ') }} FCFA</div>

        @if($quote->status === 'accepte')
            <form action="{{ route('admin.devis.convert', $quote) }}" method="POST" class="uk-margin-top">
                @csrf
                <button type="submit" class="uk-button uk-button-primary">Convertir en commande</button>
            </form>
        @endif
    @endif
</div>
@endsection
