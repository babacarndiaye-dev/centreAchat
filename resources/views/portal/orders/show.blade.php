@extends('layouts.portal')

@section('title', $purchaseOrder->order_number)

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="font-display text-2xl font-semibold">{{ $purchaseOrder->order_number }}</h1>
        <p class="text-sm text-terroir-dark/60">Commande du {{ $purchaseOrder->order_date->format('d/m/Y') }}</p>
    </div>
    <div class="flex items-center gap-3">
        <span class="rounded-full bg-terroir-cream px-4 py-1.5 text-sm font-semibold text-terroir-green">{{ \App\Models\PurchaseOrder::STATUSES[$purchaseOrder->status] }}</span>
        <a href="{{ route('portail.commandes.document', $purchaseOrder) }}" target="_blank" class="btn-outline">Télécharger / Imprimer</a>
    </div>
</div>

@if($purchaseOrder->status === 'envoyee')
    <div class="card mt-6 flex flex-wrap items-center justify-between gap-4 border-2 border-terroir-gold bg-terroir-gold/10 p-6">
        <div>
            <p class="font-semibold text-terroir-brown">Cette commande attend votre confirmation de disponibilité.</p>
            <p class="text-sm text-terroir-dark/60">Merci de confirmer que vous pouvez honorer cette commande aux quantités indiquées.</p>
        </div>
        <form action="{{ route('portail.commandes.confirm', $purchaseOrder) }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit" class="btn-primary">Confirmer la disponibilité</button>
        </form>
    </div>
@endif

<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <div class="card p-6 lg:col-span-2">
        <h2 class="font-display text-base font-semibold">Articles commandés</h2>
        <table class="mt-4 w-full text-sm">
            <thead>
                <tr class="text-left text-terroir-dark/50">
                    <th class="pb-2">Produit</th>
                    <th class="pb-2 text-right">Commandé</th>
                    <th class="pb-2 text-right">Reçu</th>
                    <th class="pb-2 text-right">Prix unitaire</th>
                    <th class="pb-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-terroir-green/10">
                @foreach($purchaseOrder->items as $item)
                    <tr>
                        <td class="py-2">{{ $item->product->name }}</td>
                        <td class="py-2 text-right">{{ $item->quantity_ordered }}</td>
                        <td class="py-2 text-right">{{ $item->quantity_received }}</td>
                        <td class="py-2 text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                        <td class="py-2 text-right font-medium">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4 ml-auto max-w-xs text-right text-base font-bold text-terroir-green">
            Total : {{ number_format($purchaseOrder->total, 0, ',', ' ') }} FCFA
        </div>

        @if($purchaseOrder->receptions->isNotEmpty())
            <div class="mt-6 border-t border-terroir-green/10 pt-6">
                <h2 class="font-display text-base font-semibold">Historique des réceptions</h2>
                <div class="mt-3 space-y-3">
                    @foreach($purchaseOrder->receptions as $reception)
                        <div class="rounded-lg bg-terroir-cream/60 p-4 text-sm">
                            <p class="font-medium">{{ $reception->reception_date->format('d/m/Y') }}</p>
                            <ul class="mt-1 text-xs text-terroir-dark/70">
                                @foreach($reception->items as $item)
                                    <li>{{ $item->orderItem->product->name }} — {{ $item->quantity_received }} ({{ $item->quality_status === 'conforme' ? 'Conforme' : 'Non conforme' }})</li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="card p-6">
        <h2 class="font-display text-base font-semibold">Paiements</h2>
        <dl class="mt-3 space-y-2 text-sm">
            <div><dt class="text-terroir-dark/50">Total</dt><dd class="font-medium">{{ number_format($purchaseOrder->total, 0, ',', ' ') }} FCFA</dd></div>
            <div><dt class="text-terroir-dark/50">Payé</dt><dd class="font-medium text-terroir-green">{{ number_format($purchaseOrder->amount_paid, 0, ',', ' ') }} FCFA</dd></div>
            <div><dt class="text-terroir-dark/50">Solde à recevoir</dt><dd class="font-medium text-terroir-terracotta">{{ number_format($purchaseOrder->balance(), 0, ',', ' ') }} FCFA</dd></div>
        </dl>

        @if($purchaseOrder->payments->isNotEmpty())
            <ul class="mt-4 space-y-1 border-t border-terroir-green/10 pt-3 text-xs text-terroir-dark/60">
                @foreach($purchaseOrder->payments as $payment)
                    <li class="flex justify-between"><span>{{ $payment->payment_date->format('d/m/Y') }}</span><span>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span></li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
