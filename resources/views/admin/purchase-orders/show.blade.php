@extends('layouts.admin')

@section('title', $purchaseOrder->order_number)

@section('content')
@php
    $poStatusColors = [
        'brouillon' => ['bg' => 'rgba(240,169,59,.15)', 'color' => '#F0A93B'],
        'envoyee' => ['bg' => 'rgba(240,169,59,.15)', 'color' => '#F0A93B'],
        'confirmee' => ['bg' => 'rgba(31,35,40,.08)', 'color' => 'rgba(31,35,40,.6)'],
        'partiellement_recue' => ['bg' => 'rgba(240,169,59,.15)', 'color' => '#F0A93B'],
        'recue' => ['bg' => 'rgba(29,138,78,.12)', 'color' => '#1D8A4E'],
        'annulee' => ['bg' => 'rgba(232,96,79,.12)', 'color' => '#E8604F'],
    ];
@endphp
<div class="uk-flex uk-flex-wrap uk-flex-middle uk-flex-between" style="gap:16px;">
    <div>
        <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.5rem;">{{ $purchaseOrder->order_number }}</h2>
        <p class="uk-text-small uk-text-muted">Fournisseur : <a href="{{ route('admin.fournisseurs.show', $purchaseOrder->supplier) }}" style="font-weight:600; color:#1D8A4E;">{{ $purchaseOrder->supplier->name }}</a></p>
    </div>
    <span class="uk-label" style="background:{{ $poStatusColors[$purchaseOrder->status]['bg'] }}; color:{{ $poStatusColors[$purchaseOrder->status]['color'] }}; padding:8px 16px; font-size:.875rem;">{{ \App\Models\PurchaseOrder::STATUSES[$purchaseOrder->status] }}</span>
</div>

<div class="uk-grid-small uk-margin-top" uk-grid>
    <div class="uk-width-2-3@l">
        <div class="uk-card uk-card-default" style="padding:24px;">
            <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Articles</h3>
            <table class="uk-table uk-table-divider uk-table-middle uk-margin-small-top">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th class="uk-text-right">Commandé</th>
                        <th class="uk-text-right">Reçu</th>
                        <th class="uk-text-right">Prix unitaire</th>
                        <th class="uk-text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td class="uk-text-right">{{ $item->quantity_ordered }}</td>
                            <td class="uk-text-right" style="font-weight:600; color:{{ $item->quantity_received < $item->quantity_ordered ? '#E8604F' : '#1D8A4E' }};">{{ $item->quantity_received }}</td>
                            <td class="uk-text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                            <td class="uk-text-right" style="font-weight:600;">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="uk-margin-top uk-text-right" style="margin-left:auto; max-width:20rem; font-size:1rem; font-weight:700; color:#1D8A4E;">
                Total : {{ number_format($purchaseOrder->total, 0, ',', ' ') }} FCFA
            </div>
            @if($purchaseOrder->notes)
                <div class="uk-margin-top" style="border-radius:8px; background:#F7F8F5; padding:16px; font-size:.875rem;">{{ $purchaseOrder->notes }}</div>
            @endif
        </div>

        @if(!in_array($purchaseOrder->status, ['recue', 'annulee']))
            <div class="uk-card uk-card-default uk-margin-top" style="padding:24px;">
                <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Enregistrer une réception</h3>
                <p class="uk-text-small uk-text-muted">Indiquez les quantités reçues et leur conformité. Le stock sera mis à jour automatiquement pour les articles conformes.</p>

                <form action="{{ route('admin.bons-commande.receptions.store', $purchaseOrder) }}" method="POST" class="uk-margin-top">
                    @csrf
                    <input type="date" name="reception_date" value="{{ now()->format('Y-m-d') }}" required class="uk-input" style="max-width:16rem;">

                    <div class="uk-margin-top" style="display:flex; flex-direction:column; gap:12px;">
                        @foreach($purchaseOrder->items as $item)
                            @if($item->remainingQuantity() > 0)
                                <div class="uk-flex uk-flex-wrap uk-flex-middle" style="gap:12px; border-radius:8px; background:rgba(247,248,245,.8); padding:10px 16px;">
                                    <span style="flex:1; font-size:.875rem; font-weight:600;">{{ $item->product->name }} <span class="uk-text-small uk-text-muted">(reste {{ $item->remainingQuantity() }})</span></span>
                                    <input type="number" name="quantity_received[{{ $item->id }}]" min="0" max="{{ $item->remainingQuantity() }}" placeholder="Qté reçue" class="uk-input" style="width:7rem;">
                                    <select name="quality_status[{{ $item->id }}]" class="uk-select" style="width:10rem;">
                                        <option value="conforme">Conforme</option>
                                        <option value="non_conforme">Non conforme</option>
                                    </select>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <textarea name="notes" rows="2" placeholder="Notes de contrôle qualité (optionnel)" class="uk-textarea uk-margin-top"></textarea>

                    <button type="submit" class="uk-button uk-button-primary uk-margin-top">Enregistrer la réception</button>
                </form>
            </div>
        @endif

        @if($purchaseOrder->receptions->isNotEmpty())
            <div class="uk-card uk-card-default uk-margin-top" style="padding:24px;">
                <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Historique des réceptions</h3>
                <div class="uk-margin-top" style="display:flex; flex-direction:column; gap:16px;">
                    @foreach($purchaseOrder->receptions as $reception)
                        <div style="border:1px solid rgba(31,35,40,.08); border-radius:8px; padding:16px;">
                            <div class="uk-flex uk-flex-middle uk-flex-between" style="font-size:.875rem;">
                                <span style="font-weight:600;">{{ $reception->reception_date->format('d/m/Y') }}</span>
                                <span class="uk-label" style="background:#F7F8F5; color:#1F2328;">{{ \App\Models\PurchaseReception::QUALITY_STATUSES[$reception->quality_status] }}</span>
                            </div>
                            <ul class="uk-margin-small-top uk-list" style="font-size:.75rem; color:rgba(31,35,40,.7);">
                                @foreach($reception->items as $item)
                                    <li>{{ $item->orderItem->product->name }} — {{ $item->quantity_received }} ({{ $item->quality_status === 'conforme' ? 'Conforme' : 'Non conforme' }})</li>
                                @endforeach
                            </ul>
                            @if($reception->notes)
                                <p class="uk-margin-small-top uk-text-muted" style="font-size:.75rem; font-style:italic;">{{ $reception->notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="uk-width-1-3@l">
        <div class="uk-card uk-card-default" style="padding:24px;">
            <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Statut</h3>
            <dl class="uk-margin-small-top" style="font-size:.875rem;">
                <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Date de commande</dt><dd style="font-weight:600; margin-top:2px;">{{ $purchaseOrder->order_date->format('d/m/Y') }}</dd></div>
                @if($purchaseOrder->expected_date)
                    <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Livraison attendue</dt><dd style="font-weight:600; margin-top:2px;">{{ $purchaseOrder->expected_date->format('d/m/Y') }}</dd></div>
                @endif
            </dl>

            @if($purchaseOrder->status === 'brouillon')
                <form action="{{ route('admin.bons-commande.status', $purchaseOrder) }}" method="POST" class="uk-margin-top">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="envoyee">
                    <button type="submit" class="uk-button uk-button-primary uk-width-1-1">Envoyer au fournisseur</button>
                </form>
            @endif

            @if($purchaseOrder->status === 'envoyee')
                <form action="{{ route('admin.bons-commande.status', $purchaseOrder) }}" method="POST" class="uk-margin-top">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="confirmee">
                    <button type="submit" class="uk-button uk-button-primary uk-width-1-1">Marquer confirmée</button>
                </form>
            @endif

            @if(!in_array($purchaseOrder->status, ['recue', 'annulee']))
                <form action="{{ route('admin.bons-commande.status', $purchaseOrder) }}" method="POST" class="uk-margin-small-top" onsubmit="return confirm('Annuler ce bon de commande ?')">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="annulee">
                    <button type="submit" class="uk-button uk-button-default uk-width-1-1">Annuler le bon de commande</button>
                </form>
            @endif
        </div>

        <div class="uk-card uk-card-default uk-margin-top" style="padding:24px;">
            <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Paiement fournisseur</h3>
            <dl class="uk-margin-small-top" style="font-size:.875rem;">
                <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Total</dt><dd style="font-weight:600; margin-top:2px;">{{ number_format($purchaseOrder->total, 0, ',', ' ') }} FCFA</dd></div>
                <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Payé</dt><dd style="font-weight:600; margin-top:2px; color:#1D8A4E;">{{ number_format($purchaseOrder->amount_paid, 0, ',', ' ') }} FCFA</dd></div>
                <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Solde dû</dt><dd style="font-weight:600; margin-top:2px; color:#E8604F;">{{ number_format($purchaseOrder->balance(), 0, ',', ' ') }} FCFA</dd></div>
            </dl>

            @if($purchaseOrder->balance() > 0)
                <form action="{{ route('admin.fournisseurs.paiements.store', $purchaseOrder->supplier) }}" method="POST" class="uk-margin-top" style="display:flex; flex-direction:column; gap:8px;">
                    @csrf
                    <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">
                    <input type="number" step="0.01" name="amount" placeholder="Montant" max="{{ $purchaseOrder->balance() }}" required class="uk-input">
                    <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required class="uk-input">
                    <input type="text" name="method" placeholder="Mode de paiement" class="uk-input">
                    <select name="payment_account_id" class="uk-select">
                        <option value="">Compte de paiement (pour l'écriture comptable)</option>
                        @foreach(\App\Models\PaymentAccount::where('is_active', true)->get() as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="uk-button uk-button-primary uk-width-1-1">Enregistrer le paiement</button>
                </form>
            @endif

            @if($purchaseOrder->payments->isNotEmpty())
                <ul class="uk-margin-top uk-list" style="border-top:1px solid rgba(31,35,40,.08); padding-top:12px; font-size:.75rem; color:rgba(31,35,40,.6);">
                    @foreach($purchaseOrder->payments as $payment)
                        <li class="uk-flex uk-flex-between"><span>{{ $payment->payment_date->format('d/m/Y') }}</span><span>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span></li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
