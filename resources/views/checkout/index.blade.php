@extends('layouts.app')

@section('title', "Finaliser la commande — DIABA HOTEL")

@section('content')
<section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
    <h1 class="section-title text-center">Finaliser votre commande</h1>

    <div class="mt-10 grid gap-10 lg:grid-cols-3" x-data="{
        zoneId: '{{ $selectedZone?->id }}',
        zones: {{ $deliveryZones->map(fn ($z) => ['id' => (string) $z->id, 'fee' => (float) $z->fee, 'free_above' => $z->free_above ? (float) $z->free_above : null])->toJson() }},
        subtotal: {{ $subtotal }},
        taxRate: {{ $taxRate?->rate ?? 0 }},
        fee() {
            const zone = this.zones.find(z => z.id === this.zoneId);
            if (!zone) return 0;
            if (zone.free_above !== null && this.subtotal >= zone.free_above) return 0;
            return zone.fee;
        },
        tax() { return Math.round(this.subtotal * (this.taxRate / 100)) },
        total() { return this.subtotal + this.fee() + this.tax() },
        fmt(n) { return new Intl.NumberFormat('fr-FR').format(Math.round(n)) + ' FCFA' }
    }">
        <form action="{{ route('commande.store') }}" method="POST" class="card space-y-5 p-8 lg:col-span-2">
            @csrf

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="label" for="customer_name">Nom complet</label>
                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" required class="input">
                    @error('customer_name') <p class="mt-1 text-xs text-terroir-terracotta">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="customer_phone">Téléphone</label>
                    <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" required class="input">
                    @error('customer_phone') <p class="mt-1 text-xs text-terroir-terracotta">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="label" for="customer_email">E-mail (optionnel)</label>
                <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" class="input">
                @error('customer_email') <p class="mt-1 text-xs text-terroir-terracotta">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label" for="delivery_address">Adresse de livraison</label>
                <textarea id="delivery_address" name="delivery_address" rows="3" required class="input">{{ old('delivery_address') }}</textarea>
                @error('delivery_address') <p class="mt-1 text-xs text-terroir-terracotta">{{ $message }}</p> @enderror
            </div>

            @if($deliveryZones->isNotEmpty())
                <div>
                    <label class="label" for="delivery_zone_id">Zone de livraison</label>
                    <select id="delivery_zone_id" name="delivery_zone_id" x-model="zoneId" class="input">
                        @foreach($deliveryZones as $zone)
                            <option value="{{ $zone->id }}" @selected($selectedZone?->id === $zone->id)>{{ $zone->name }} — {{ $zone->fee > 0 ? number_format($zone->fee, 0, ',', ' ').' FCFA' : 'Gratuit' }}</option>
                        @endforeach
                    </select>
                    @error('delivery_zone_id') <p class="mt-1 text-xs text-terroir-terracotta">{{ $message }}</p> @enderror
                </div>
            @endif

            <input type="hidden" name="payment_method" value="especes">

            <button type="submit" class="btn-primary w-full justify-center">Confirmer ma commande</button>
        </form>

        <div class="card h-fit p-6">
            <h2 class="font-display text-lg font-semibold">Récapitulatif</h2>
            <ul class="mt-4 space-y-3 text-sm">
                @foreach($items as $item)
                    <li class="flex justify-between gap-3">
                        <span class="text-terroir-dark/70">{{ $item->quantity }} × {{ $item->product->name }}</span>
                        <span class="font-medium">{{ number_format($item->total, 0, ',', ' ') }} FCFA</span>
                    </li>
                @endforeach
            </ul>
            <div class="mt-6 space-y-2 border-t border-terroir-green/10 pt-4 text-sm">
                <div class="flex justify-between"><span class="text-terroir-dark/60">Sous-total</span><span x-text="fmt(subtotal)"></span></div>
                @if($taxRate)
                    <div class="flex justify-between"><span class="text-terroir-dark/60">{{ $taxRate->name }} ({{ $taxRate->rate }}%)</span><span x-text="fmt(tax())"></span></div>
                @endif
                <div class="flex justify-between"><span class="text-terroir-dark/60">Livraison</span><span x-text="fee() > 0 ? fmt(fee()) : 'Offerte'"></span></div>
                <div class="flex justify-between text-base font-bold text-terroir-green"><span>Total</span><span x-text="fmt(total())"></span></div>
            </div>
        </div>
    </div>
</section>
@endsection
