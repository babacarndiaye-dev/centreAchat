@extends('layouts.admin')

@section('title', $quote->quote_number)

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <h2 class="font-display text-2xl font-semibold">{{ $quote->quote_number }}</h2>
        <p class="text-sm text-terroir-dark/50">Demandé par {{ $quote->user->name }} ({{ $quote->user->company_name ?? $quote->user->email }})</p>
    </div>
    <span class="{{ $quote->statusBadgeClass() }} px-4 py-1.5 text-sm">{{ \App\Models\Quote::STATUSES[$quote->status] }}</span>
</div>

@if($quote->notes)
    <div class="admin-card mt-6 text-sm">{{ $quote->notes }}</div>
@endif

<div class="admin-card mt-6">
    <h3 class="font-display text-base font-semibold">Articles demandés</h3>

    @if(in_array($quote->status, ['en_attente', 'envoye']))
        <form action="{{ route('admin.devis.send', $quote) }}" method="POST" class="mt-3">
            @csrf
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th class="text-right">Quantité</th>
                        <th class="text-right">Prix unitaire proposé</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quote->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right">
                                <input type="number" step="0.01" name="unit_price[{{ $item->id }}]" value="{{ $item->unit_price ?? $item->product->professional_price ?? $item->product->price }}" required class="input w-32 text-right">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 max-w-xs">
                <label class="label" for="valid_until">Valable jusqu'au</label>
                <input type="date" id="valid_until" name="valid_until" value="{{ now()->addDays(7)->format('Y-m-d') }}" class="input">
            </div>

            <button type="submit" class="btn-primary mt-4">Envoyer le devis au client</button>
        </form>
    @else
        <table class="admin-table mt-3">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="text-right">Quantité</th>
                    <th class="text-right">Prix unitaire</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                        <td class="text-right font-semibold">{{ number_format($item->total(), 0, ',', ' ') }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4 text-right text-base font-bold text-terroir-green">Total : {{ number_format($quote->total, 0, ',', ' ') }} FCFA</div>

        @if($quote->status === 'accepte')
            <form action="{{ route('admin.devis.convert', $quote) }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="btn-primary">Convertir en commande</button>
            </form>
        @endif
    @endif
</div>
@endsection
