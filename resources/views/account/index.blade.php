@extends('layouts.app')

@section('title', "Mon compte — DIABA HOTEL Produits du Sénégal (D.H.P.S)")

@section('content')
<section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="section-eyebrow">Bienvenue</span>
            <h1 class="section-title mt-2">{{ auth()->user()->name }}</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            @if(config('services.vapid.public_key'))
                <button
                    x-data="{
                        status: 'unsupported',
                        async refresh() { this.status = window.DiabaHotelPush ? await window.DiabaHotelPush.status() : 'unsupported'; },
                        async toggle() {
                            if (!window.DiabaHotelPush) return;
                            if (this.status === 'subscribed') {
                                await window.DiabaHotelPush.unsubscribe();
                            } else {
                                await window.DiabaHotelPush.subscribe('{{ config('services.vapid.public_key') }}');
                            }
                            await this.refresh();
                        },
                    }"
                    x-init="setTimeout(() => refresh(), 300)"
                    x-show="status !== 'unsupported'"
                    x-cloak
                    @click="toggle()"
                    type="button"
                    class="btn-outline"
                >
                    <span x-show="status === 'subscribed'">🔔 Notifications activées</span>
                    <span x-show="status !== 'subscribed'">🔕 Activer les notifications</span>
                </button>
            @endif
            <a href="{{ route('compte.messages.index') }}" class="btn-outline">💬 Mes conversations</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-outline">Se déconnecter</button>
            </form>
        </div>
    </div>

    @if(auth()->user()->isProfessionalType())
        <div class="card mt-8 p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="section-eyebrow">Compte professionnel</span>
                    @if(auth()->user()->b2b_status === 'en_attente')
                        <p class="mt-2 text-sm text-terroir-brown">Votre compte est en attente de validation par notre équipe. Certaines fonctionnalités (devis, commandes récurrentes, tarifs pro) seront disponibles après validation.</p>
                    @elseif(auth()->user()->b2b_status === 'refuse')
                        <p class="mt-2 text-sm text-terroir-terracotta">Votre demande de compte professionnel n'a pas été validée. Contactez-nous pour plus d'informations.</p>
                    @elseif(auth()->user()->b2b_status === 'valide')
                        <p class="mt-2 text-sm text-terroir-green">Compte professionnel validé — vous bénéficiez des tarifs pro et des services dédiés.</p>
                    @endif
                </div>
                @if(auth()->user()->isApprovedB2B())
                    <div class="flex gap-3">
                        <a href="{{ route('compte.devis.index') }}" class="btn-outline">Mes devis</a>
                        <a href="{{ route('compte.commandes-recurrentes.index') }}" class="btn-outline">Commandes récurrentes</a>
                    </div>
                @endif
            </div>

            @if(auth()->user()->isApprovedB2B() && auth()->user()->credit_limit)
                <div class="mt-5 grid grid-cols-3 gap-4 border-t border-terroir-green/10 pt-5 text-sm">
                    <div><p class="text-terroir-dark/50">Plafond de crédit</p><p class="font-semibold">{{ number_format(auth()->user()->credit_limit, 0, ',', ' ') }} FCFA</p></div>
                    <div><p class="text-terroir-dark/50">Utilisé</p><p class="font-semibold text-terroir-terracotta">{{ number_format(auth()->user()->creditUsed(), 0, ',', ' ') }} FCFA</p></div>
                    <div><p class="text-terroir-dark/50">Disponible</p><p class="font-semibold text-terroir-green">{{ number_format(auth()->user()->creditAvailable(), 0, ',', ' ') }} FCFA</p></div>
                </div>
            @endif
        </div>
    @endif

    <h2 class="mt-12 font-display text-xl font-semibold">Mes commandes</h2>

    @if($orders->isEmpty())
        <p class="mt-4 text-terroir-dark/60">Vous n'avez pas encore passé de commande.</p>
    @else
        <div class="mt-6 space-y-4">
            @foreach($orders as $order)
                <div class="card flex flex-wrap items-center justify-between gap-4 p-5">
                    <div>
                        <p class="font-semibold">{{ $order->order_number }}</p>
                        <p class="text-sm text-terroir-dark/50">{{ $order->created_at->format('d/m/Y') }}</p>
                    </div>
                    <span class="rounded-full bg-terroir-cream px-4 py-1.5 text-xs font-semibold text-terroir-green">{{ \App\Models\Order::STATUSES[$order->status] ?? $order->status }}</span>
                    <span class="font-bold text-terroir-green">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $orders->links() }}</div>
    @endif
</section>
@endsection
