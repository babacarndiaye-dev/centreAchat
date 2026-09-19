@extends('layouts.admin')

@section('title', 'Commande '.$order->order_number)

@section('content')

<div
    class="uk-card uk-card-default uk-margin-bottom"
    style="padding:24px;"
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
    <div class="uk-flex uk-flex-between uk-flex-middle">
        <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">🧠 Assistant commande</h2>
        <button
            type="button"
            @click="fetchSuggestion()"
            :disabled="loading"
            style="font-size:.875rem; font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;"
        >
            <span x-show="!loading" x-text="checked ? 'Réanalyser' : 'Analyser avec l\'IA'"></span>
            <span x-show="loading">Analyse en cours…</span>
        </button>
    </div>
    <p class="uk-text-small uk-text-muted uk-margin-small-top">Si l'IA juge un message au client utile, il est envoyé automatiquement via le chat — sans validation supplémentaire.</p>

    @if(count($flags))
        <ul class="uk-margin-top" style="list-style:none; padding:0; display:flex; flex-direction:column; gap:8px;">
            @foreach($flags as $flag)
                <li class="uk-flex" style="gap:8px; align-items:flex-start; border-radius:8px; padding:8px 12px; font-size:.875rem; {{ $flag['severity'] === 'critical' ? 'background:rgba(232,96,79,.1); color:#E8604F;' : 'background:rgba(240,169,59,.1); color:#8a5a1f;' }}">
                    <span>{{ $flag['severity'] === 'critical' ? '⚠️' : '👁' }}</span>
                    <span>{{ $flag['label'] }}</span>
                </li>
            @endforeach
        </ul>
    @else
        <p class="uk-text-small uk-text-muted uk-margin-small-top">Aucune alerte détectée sur cette commande.</p>
    @endif

    <div x-show="checked" x-cloak class="uk-margin-top" style="display:flex; flex-direction:column; gap:12px; border-top:1px solid rgba(31,35,40,.08); padding-top:16px;">
        <template x-if="available && note">
            <div>
                <p class="uk-text-small uk-text-muted" style="font-size:.6875rem; font-weight:600; text-transform:uppercase; letter-spacing:.03em;">Note pour l'équipe</p>
                <p class="uk-margin-small-top uk-text-small" style="white-space:pre-line;" x-text="note"></p>
            </div>
        </template>
        <template x-if="sent && customerMessage">
            <div style="border-radius:8px; background:rgba(29,138,78,.1); padding:10px 12px;">
                <p style="font-size:.6875rem; font-weight:600; text-transform:uppercase; letter-spacing:.03em; color:#1D8A4E;">✅ Message envoyé au client</p>
                <p class="uk-margin-small-top uk-text-small" style="white-space:pre-line;" x-text="customerMessage"></p>
            </div>
        </template>
        <template x-if="!sent && customerMessage">
            <div style="border-radius:8px; background:rgba(240,169,59,.1); padding:10px 12px;">
                <p style="font-size:.6875rem; font-weight:600; text-transform:uppercase; letter-spacing:.03em; color:#8a5a1f;">Message rédigé mais non envoyé</p>
                <p class="uk-margin-small-top uk-text-small" style="white-space:pre-line;" x-text="customerMessage"></p>
                <p class="uk-text-small uk-text-muted uk-margin-small-top">Ce client n'a pas de compte associé à la commande — l'envoi automatique via le chat n'est possible que pour les clients connectés.</p>
            </div>
        </template>
        <template x-if="!available">
            <p class="uk-text-small uk-text-muted">Suggestion IA indisponible pour le moment (aucune clé configurée ou service temporairement inaccessible) — les alertes ci-dessus restent fiables, elles ne dépendent pas de l'IA.</p>
        </template>
    </div>
</div>

<div class="uk-grid-small uk-child-width-1-1 uk-child-width-2-3@l" uk-grid>
    <div>
        <div class="uk-card uk-card-default" style="padding:24px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Articles</h2>
            <table class="uk-table uk-table-divider uk-margin-small-top">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th class="uk-text-right">Prix unitaire</th>
                        <th class="uk-text-right">Quantité</th>
                        <th class="uk-text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                {{ $item->product_name }}
                                @if($item->price_tier !== 'retail')
                                    <span class="uk-label" style="background:rgba(240,169,59,.2); color:#8a5a1f; margin-left:4px; font-size:.625rem;">{{ $item->priceTierLabel() }}</span>
                                @endif
                            </td>
                            <td class="uk-text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                            <td class="uk-text-right">{{ $item->quantity }}</td>
                            <td class="uk-text-right" style="font-weight:600;">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="uk-margin-top" style="margin-left:auto; max-width:20rem; display:flex; flex-direction:column; gap:8px; border-top:1px solid rgba(31,35,40,.08); padding-top:16px; font-size:.875rem;">
                <div class="uk-flex uk-flex-between"><span class="uk-text-muted">Sous-total</span><span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span></div>
                <div class="uk-flex uk-flex-between"><span class="uk-text-muted">Livraison</span><span>{{ number_format($order->delivery_fee, 0, ',', ' ') }} FCFA</span></div>
                <div class="uk-flex uk-flex-between" style="font-size:1rem; font-weight:700; color:#1D8A4E;"><span>Total</span><span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span></div>
            </div>

            @if($order->notes)
                <div class="uk-margin-top uk-text-small" style="border-radius:8px; background:#F7F8F5; padding:16px;">
                    <strong>Notes :</strong> {{ $order->notes }}
                </div>
            @endif
        </div>
    </div>

    <div>
        <div class="uk-card uk-card-default" style="padding:24px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Client</h2>
            <dl class="uk-margin-top uk-text-small" style="display:flex; flex-direction:column; gap:8px;">
                <div><dt class="uk-text-muted">Nom</dt><dd style="font-weight:600;">{{ $order->customer_name }}</dd></div>
                <div><dt class="uk-text-muted">Téléphone</dt><dd style="font-weight:600;">{{ $order->customer_phone }}</dd></div>
                @if($order->customer_email)
                    <div><dt class="uk-text-muted">E-mail</dt><dd style="font-weight:600;">{{ $order->customer_email }}</dd></div>
                @endif
                <div><dt class="uk-text-muted">Adresse</dt><dd style="font-weight:600;">{{ $order->delivery_address }}, {{ $order->city }}</dd></div>
                @if($order->hotel_name)
                    <div><dt class="uk-text-muted">🧳 Livraison hôtel</dt><dd style="font-weight:600;">{{ $order->hotel_name }}{{ $order->room_number ? ' — Chambre '.$order->room_number : '' }}</dd></div>
                @endif
                @if($order->gift_message)
                    <div><dt class="uk-text-muted">🎁 Message cadeau</dt><dd style="border-radius:8px; background:rgba(240,169,59,.1); padding:8px; font-weight:600; font-style:italic;">« {{ $order->gift_message }} »</dd></div>
                @endif
                <div><dt class="uk-text-muted">Paiement</dt><dd style="font-weight:600;">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }} — {{ \App\Models\Order::PAYMENT_STATUSES[$order->payment_status] ?? $order->payment_status }}</dd></div>
            </dl>
        </div>

        @if($order->payment_status !== 'paye')
            <div class="uk-card uk-card-default uk-margin-top" style="padding:24px;">
                <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Enregistrer un paiement</h2>
                <p class="uk-text-small uk-text-muted uk-margin-small-top">Génère automatiquement l'écriture comptable d'encaissement.</p>
                <form action="{{ route('admin.commandes.payment', $order) }}" method="POST" class="uk-margin-top" style="display:flex; flex-direction:column; gap:12px;">
                    @csrf
                    <select name="payment_account_id" required class="uk-select">
                        <option value="">Compte de paiement</option>
                        @foreach($paymentAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" step="0.01" name="amount" value="{{ $order->total - $order->amountPaid() }}" max="{{ $order->total }}" required class="uk-input">
                    <button type="submit" class="uk-button uk-button-primary uk-width-1-1">Encaisser</button>
                </form>
            </div>
        @endif

        <div class="uk-card uk-card-default uk-margin-top" style="padding:24px;">
            <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Statut de la commande</h2>
            <form action="{{ route('admin.commandes.status', $order) }}" method="POST" class="uk-margin-top" style="display:flex; flex-direction:column; gap:12px;">
                @csrf @method('PATCH')
                <select name="status" class="uk-select">
                    @foreach(\App\Models\Order::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="uk-button uk-button-primary uk-width-1-1">Mettre à jour</button>
            </form>
        </div>
    </div>
</div>
@endsection
