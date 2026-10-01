@extends('layouts.app')

@section('title', "Commande confirmée — DIABA HOTEL Produits du Sénégal (D.H.P.S)")

@section('content')
<section class="mx-auto max-w-2xl px-4 py-24 text-center sm:px-6 lg:px-8">
    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-terroir-green text-4xl text-white">✓</div>
    <h1 class="section-title mt-6">Merci {{ $order->customer_name }} !</h1>
    <p class="mt-3 text-terroir-dark/70">Votre commande <strong>{{ $order->order_number }}</strong> a bien été enregistrée. Nous vous contacterons rapidement au {{ $order->customer_phone }}.</p>

    <div class="card mt-10 p-6 text-left">
        <ul class="space-y-3 text-sm">
            @foreach($order->items as $item)
                <li class="flex justify-between">
                    <span>{{ $item->quantity }} × {{ $item->product_name }}</span>
                    <span class="font-medium">{{ number_format($item->total, 0, ',', ' ') }} FCFA</span>
                </li>
            @endforeach
        </ul>
        <div class="mt-6 space-y-2 border-t border-terroir-green/10 pt-4 text-sm">
            <div class="flex justify-between"><span class="text-terroir-dark/60">Sous-total</span><span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span></div>
            <div class="flex justify-between"><span class="text-terroir-dark/60">Livraison</span><span>{{ $order->delivery_fee > 0 ? number_format($order->delivery_fee, 0, ',', ' ').' FCFA' : 'Offerte' }}</span></div>
            <div class="flex justify-between text-base font-bold text-terroir-green"><span>Total</span><span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span></div>
        </div>
    </div>

    <a href="{{ route('produits.index') }}" class="btn-primary mt-10">Continuer mes achats</a>
</section>
@endsection
