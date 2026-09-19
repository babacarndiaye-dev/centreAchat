@extends('layouts.app')

@section('title', $quote->quote_number)

@section('content')
<section class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
    <a href="{{ route('compte.devis.index') }}" class="text-sm text-terroir-dark/60 hover:text-terroir-terracotta">← Mes devis</a>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
        <h1 class="section-title">{{ $quote->quote_number }}</h1>
        <span class="rounded-full bg-terroir-cream px-4 py-1.5 text-sm font-semibold text-terroir-green">{{ \App\Models\Quote::STATUSES[$quote->status] }}</span>
    </div>

    <div class="card mt-8 p-8">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-terroir-dark/50">
                    <th class="pb-2">Produit</th>
                    <th class="pb-2 text-right">Quantité</th>
                    <th class="pb-2 text-right">Prix unitaire</th>
                    <th class="pb-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-terroir-green/10">
                @foreach($quote->items as $item)
                    <tr>
                        <td class="py-2">{{ $item->product->name }}</td>
                        <td class="py-2 text-right">{{ $item->quantity }}</td>
                        <td class="py-2 text-right">{{ $item->unit_price ? number_format($item->unit_price, 0, ',', ' ').' FCFA' : 'À définir' }}</td>
                        <td class="py-2 text-right font-medium">{{ $item->unit_price ? number_format($item->total(), 0, ',', ' ').' FCFA' : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($quote->total > 0)
            <div class="mt-4 ml-auto max-w-xs text-right text-base font-bold text-terroir-green">
                Total : {{ number_format($quote->total, 0, ',', ' ') }} FCFA
            </div>
        @endif

        @if($quote->valid_until)
            <p class="mt-2 text-right text-xs text-terroir-dark/50">Valable jusqu'au {{ $quote->valid_until->format('d/m/Y') }}</p>
        @endif

        @if($quote->notes)
            <div class="mt-6 rounded-lg bg-terroir-cream p-4 text-sm">{{ $quote->notes }}</div>
        @endif

        @if($quote->status === 'envoye')
            <div class="mt-8 flex gap-3">
                <form action="{{ route('compte.devis.accept', $quote) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-primary">Accepter le devis</button>
                </form>
                <form action="{{ route('compte.devis.refuse', $quote) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-outline">Refuser</button>
                </form>
            </div>
        @elseif($quote->status === 'en_attente')
            <p class="mt-8 text-sm text-terroir-dark/60">Votre demande est en cours de traitement, notre équipe vous enverra une offre tarifaire prochainement.</p>
        @elseif($quote->status === 'accepte')
            <p class="mt-8 text-sm text-terroir-green">Devis accepté — notre équipe vous contactera pour finaliser la commande.</p>
        @endif
    </div>
</section>
@endsection
