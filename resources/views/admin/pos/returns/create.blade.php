@extends('layouts.admin')

@section('title', 'Nouveau retour')

@section('content')
<div class="admin-card max-w-2xl">
    <h2 class="font-display text-lg font-semibold">Rechercher une commande</h2>
    <form method="GET" class="mt-3 flex gap-2">
        <input type="text" name="order_number" value="{{ request('order_number') }}" placeholder="Numéro de commande (ex : CA-20260818-XXXXXX)" required class="input flex-1">
        <button type="submit" class="btn-outline">Rechercher</button>
    </form>
    @error('order_number') <p class="mt-1.5 text-sm text-terroir-terracotta">{{ $message }}</p> @enderror
</div>

@if($order)
    <div class="admin-card mt-6 max-w-2xl">
        <h2 class="font-display text-lg font-semibold">{{ $order->order_number }}</h2>
        <p class="text-sm text-terroir-dark/50">{{ $order->customer_name }} — {{ $order->created_at->format('d/m/Y') }}</p>

        <form action="{{ route('admin.pos.retours.store') }}" method="POST" class="mt-4">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">

            <div class="flex flex-col gap-3">
                @foreach($order->items as $item)
                    @if($item->returnableQuantity() > 0)
                        <div class="flex items-center justify-between gap-4 rounded-lg bg-terroir-cream/60 px-4 py-2.5">
                            <div>
                                <p class="text-sm font-semibold">{{ $item->product_name }}</p>
                                <p class="text-sm text-terroir-dark/50">Acheté : {{ $item->quantity }} — Retournable : {{ $item->returnableQuantity() }}</p>
                            </div>
                            <input type="number" name="quantity[{{ $item->id }}]" min="0" max="{{ $item->returnableQuantity() }}" value="0" class="input w-24">
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-4">
                <label class="label" for="notes">Motif du retour</label>
                <textarea id="notes" name="notes" rows="2" class="input"></textarea>
            </div>

            <button type="submit" class="btn-primary mt-4">Valider le retour</button>
        </form>
    </div>
@endif
@endsection
