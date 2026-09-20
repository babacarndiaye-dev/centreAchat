@extends('layouts.admin')

@section('title', 'Commande '.$order->order_number)

@section('content')

<div
    class="admin-card mb-6"
    x-data="{
        note: null,
        customerMessage: null,
        sent: false,
        loading: false,
        checked: false,
        available: true,
        fetchSuggestion() {
            this.loading = true;
            fetch('{{ route('admin.commandes.suggestion', $order) }}', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    this.available = data.available;
                    this.note = data.note;
                    this.customerMessage = data.customer_message;
                    this.sent = data.sent;
                    this.checked = true;
                    this.loading = false;
                })
                .catch(() => { this.loading = false; this.checked = true; this.available = false; });
        },
    }"
>
    <div class="flex items-center justify-between">
        <h2 class="flex items-center gap-1.5 font-display text-lg font-semibold"><span class="material-symbols-outlined">psychology</span> Assistant commande</h2>
        <button type="button" @click="fetchSuggestion()" :disabled="loading" class="text-sm font-semibold text-terroir-green disabled:opacity-50">
            <span x-show="!loading" x-text="checked ? 'Réanalyser' : 'Analyser avec l\'IA'"></span>
            <span x-show="loading">Analyse en cours…</span>
        </button>
    </div>
    <p class="mt-1.5 text-sm text-terroir-dark/50">Si l'IA juge un message au client utile, il est envoyé automatiquement via le chat — sans validation supplémentaire.</p>

    @if(count($flags))
        <ul class="mt-4 flex flex-col gap-2">
            @foreach($flags as $flag)
                <li class="flex items-start gap-2 rounded-lg px-3 py-2 text-sm {{ $flag['severity'] === 'critical' ? 'bg-terroir-terracotta/10 text-terroir-terracotta' : 'bg-terroir-gold/15 text-terroir-brown' }}">
                    <span class="material-symbols-outlined text-base">{{ $flag['severity'] === 'critical' ? 'warning' : 'visibility' }}</span>
                    <span>{{ $flag['label'] }}</span>
                </li>
            @endforeach
        </ul>
    @else
        <p class="mt-1.5 text-sm text-terroir-dark/50">Aucune alerte détectée sur cette commande.</p>
    @endif

    <div x-show="checked" x-cloak class="mt-4 flex flex-col gap-3 border-t border-terroir-dark/10 pt-4">
        <template x-if="available && note">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wide text-terroir-dark/40">Note pour l'équipe</p>
                <p class="mt-1.5 whitespace-pre-line text-sm" x-text="note"></p>
            </div>
        </template>
        <template x-if="sent && customerMessage">
            <div class="rounded-lg bg-terroir-green/10 px-3 py-2.5">
                <p class="flex items-center gap-1 text-[11px] font-semibold uppercase tracking-wide text-terroir-green"><span class="material-symbols-outlined text-sm is-filled">check_circle</span> Message envoyé au client</p>
                <p class="mt-1.5 whitespace-pre-line text-sm" x-text="customerMessage"></p>
            </div>
        </template>
        <template x-if="!sent && customerMessage">
            <div class="rounded-lg bg-terroir-gold/15 px-3 py-2.5">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-terroir-brown">Message rédigé mais non envoyé</p>
                <p class="mt-1.5 whitespace-pre-line text-sm" x-text="customerMessage"></p>
                <p class="mt-1.5 text-sm text-terroir-dark/50">Ce client n'a pas de compte associé à la commande — l'envoi automatique via le chat n'est possible que pour les clients connectés.</p>
            </div>
        </template>
        <template x-if="!available">
            <p class="text-sm text-terroir-dark/50">Suggestion IA indisponible pour le moment (aucune clé configurée ou service temporairement inaccessible) — les alertes ci-dessus restent fiables, elles ne dépendent pas de l'IA.</p>
        </template>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <div class="admin-card">
            <h2 class="font-display text-lg font-semibold">Articles</h2>
            <table class="admin-table mt-3">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th class="text-right">Prix unitaire</th>
                        <th class="text-right">Quantité</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                {{ $item->product_name }}
                                @if($item->price_tier !== 'retail')
                                    <span class="ml-1 rounded-full bg-terroir-gold/20 px-2 py-0.5 text-[10px] font-medium text-terroir-brown">{{ $item->priceTierLabel() }}</span>
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right font-semibold">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="ml-auto mt-4 flex max-w-xs flex-col gap-2 border-t border-terroir-dark/10 pt-4 text-sm">
                <div class="flex justify-between"><span class="text-terroir-dark/60">Sous-total</span><span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span></div>
                <div class="flex justify-between"><span class="text-terroir-dark/60">Livraison</span><span>{{ number_format($order->delivery_fee, 0, ',', ' ') }} FCFA</span></div>
                <div class="flex justify-between text-base font-bold text-terroir-green"><span>Total</span><span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span></div>
            </div>

            @if($order->notes)
                <div class="mt-4 rounded-lg bg-terroir-cream p-4 text-sm">
                    <strong>Notes :</strong> {{ $order->notes }}
                </div>
            @endif
        </div>
    </div>

    <div>
        <div class="admin-card">
            <h2 class="font-display text-lg font-semibold">Client</h2>
            <dl class="mt-3 flex flex-col gap-2 text-sm">
                <div><dt class="text-terroir-dark/50">Nom</dt><dd class="font-semibold">{{ $order->customer_name }}</dd></div>
                <div><dt class="text-terroir-dark/50">Téléphone</dt><dd class="font-semibold">{{ $order->customer_phone }}</dd></div>
                @if($order->customer_email)
                    <div><dt class="text-terroir-dark/50">E-mail</dt><dd class="font-semibold">{{ $order->customer_email }}</dd></div>
                @endif
                <div><dt class="text-terroir-dark/50">Adresse</dt><dd class="font-semibold">{{ $order->delivery_address }}, {{ $order->city }}</dd></div>
                @if($order->hotel_name)
                    <div><dt class="flex items-center gap-1 text-terroir-dark/50"><span class="material-symbols-outlined text-base">luggage</span> Livraison hôtel</dt><dd class="font-semibold">{{ $order->hotel_name }}{{ $order->room_number ? ' — Chambre '.$order->room_number : '' }}</dd></div>
                @endif
                @if($order->gift_message)
                    <div><dt class="flex items-center gap-1 text-terroir-dark/50"><span class="material-symbols-outlined text-base">card_giftcard</span> Message cadeau</dt><dd class="rounded-lg bg-terroir-gold/10 p-2 font-semibold italic">« {{ $order->gift_message }} »</dd></div>
                @endif
                <div><dt class="text-terroir-dark/50">Paiement</dt><dd class="font-semibold">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }} — {{ \App\Models\Order::PAYMENT_STATUSES[$order->payment_status] ?? $order->payment_status }}</dd></div>
            </dl>
        </div>

        @if($order->payment_status !== 'paye')
            <div class="admin-card mt-6">
                <h2 class="font-display text-lg font-semibold">Enregistrer un paiement</h2>
                <p class="mt-1.5 text-sm text-terroir-dark/50">Génère automatiquement l'écriture comptable d'encaissement.</p>
                <form action="{{ route('admin.commandes.payment', $order) }}" method="POST" class="mt-4 flex flex-col gap-3">
                    @csrf
                    <select name="payment_account_id" required class="input">
                        <option value="">Compte de paiement</option>
                        @foreach($paymentAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" step="0.01" name="amount" value="{{ $order->total - $order->amountPaid() }}" max="{{ $order->total }}" required class="input">
                    <button type="submit" class="btn-primary w-full justify-center">Encaisser</button>
                </form>
            </div>
        @endif

        <div class="admin-card mt-6">
            <h2 class="font-display text-lg font-semibold">Statut de la commande</h2>
            <form action="{{ route('admin.commandes.status', $order) }}" method="POST" class="mt-4 flex flex-col gap-3">
                @csrf @method('PATCH')
                <select name="status" class="input">
                    @foreach(\App\Models\Order::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary w-full justify-center">Mettre à jour</button>
            </form>
        </div>
    </div>
</div>
@endsection
