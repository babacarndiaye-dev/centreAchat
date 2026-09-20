@extends('layouts.admin')

@section('title', $purchaseOrder->order_number)

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <h2 class="font-display text-2xl font-semibold">{{ $purchaseOrder->order_number }}</h2>
        <p class="text-sm text-terroir-dark/50">Fournisseur : <a href="{{ route('admin.fournisseurs.show', $purchaseOrder->supplier) }}" class="admin-link">{{ $purchaseOrder->supplier->name }}</a></p>
    </div>
    <span class="{{ $purchaseOrder->statusBadgeClass() }} px-4 py-1.5 text-sm">{{ \App\Models\PurchaseOrder::STATUSES[$purchaseOrder->status] }}</span>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <div class="admin-card">
            <h3 class="font-display text-base font-semibold">Articles</h3>
            <table class="admin-table mt-3">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th class="text-right">Commandé</th>
                        <th class="text-right">Reçu</th>
                        <th class="text-right">Prix unitaire</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td class="text-right">{{ $item->quantity_ordered }}</td>
                            <td class="text-right font-semibold {{ $item->quantity_received < $item->quantity_ordered ? 'text-terroir-terracotta' : 'text-terroir-green' }}">{{ $item->quantity_received }}</td>
                            <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                            <td class="text-right font-semibold">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="ml-auto mt-4 max-w-xs text-right text-base font-bold text-terroir-green">
                Total : {{ number_format($purchaseOrder->total, 0, ',', ' ') }} FCFA
            </div>
            @if($purchaseOrder->notes)
                <div class="mt-4 rounded-lg bg-terroir-cream p-4 text-sm">{{ $purchaseOrder->notes }}</div>
            @endif
        </div>

        @if(!in_array($purchaseOrder->status, ['recue', 'annulee']))
            <div class="admin-card mt-6">
                <h3 class="font-display text-base font-semibold">Enregistrer une réception</h3>
                <p class="text-sm text-terroir-dark/50">Indiquez les quantités reçues et leur conformité. Le stock sera mis à jour automatiquement pour les articles conformes.</p>

                <form action="{{ route('admin.bons-commande.receptions.store', $purchaseOrder) }}" method="POST" class="mt-4">
                    @csrf
                    <input type="date" name="reception_date" value="{{ now()->format('Y-m-d') }}" required class="input max-w-xs">

                    <div class="mt-4 flex flex-col gap-3">
                        @foreach($purchaseOrder->items as $item)
                            @if($item->remainingQuantity() > 0)
                                <div class="flex flex-wrap items-center gap-3 rounded-lg bg-terroir-cream/80 px-4 py-2.5">
                                    <span class="flex-1 text-sm font-semibold">{{ $item->product->name }} <span class="text-sm font-normal text-terroir-dark/50">(reste {{ $item->remainingQuantity() }})</span></span>
                                    <input type="number" name="quantity_received[{{ $item->id }}]" min="0" max="{{ $item->remainingQuantity() }}" placeholder="Qté reçue" class="input w-28">
                                    <select name="quality_status[{{ $item->id }}]" class="input w-40">
                                        <option value="conforme">Conforme</option>
                                        <option value="non_conforme">Non conforme</option>
                                    </select>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <textarea name="notes" rows="2" placeholder="Notes de contrôle qualité (optionnel)" class="input mt-4"></textarea>

                    <button type="submit" class="btn-primary mt-4">Enregistrer la réception</button>
                </form>
            </div>
        @endif

        @if($purchaseOrder->receptions->isNotEmpty())
            <div class="admin-card mt-6">
                <h3 class="font-display text-base font-semibold">Historique des réceptions</h3>
                <div class="mt-4 flex flex-col gap-4">
                    @foreach($purchaseOrder->receptions as $reception)
                        <div class="rounded-lg border border-terroir-dark/10 p-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold">{{ $reception->reception_date->format('d/m/Y') }}</span>
                                <span class="admin-badge-neutral">{{ \App\Models\PurchaseReception::QUALITY_STATUSES[$reception->quality_status] }}</span>
                            </div>
                            <ul class="mt-1.5 space-y-0.5 text-xs text-terroir-dark/70">
                                @foreach($reception->items as $item)
                                    <li>{{ $item->orderItem->product->name }} — {{ $item->quantity_received }} ({{ $item->quality_status === 'conforme' ? 'Conforme' : 'Non conforme' }})</li>
                                @endforeach
                            </ul>
                            @if($reception->notes)
                                <p class="mt-1.5 text-xs italic text-terroir-dark/50">{{ $reception->notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div>
        <div class="admin-card">
            <h3 class="font-display text-base font-semibold">Statut</h3>
            <dl class="mt-3 space-y-2 text-sm">
                <div><dt class="text-terroir-dark/50">Date de commande</dt><dd class="mt-0.5 font-semibold">{{ $purchaseOrder->order_date->format('d/m/Y') }}</dd></div>
                @if($purchaseOrder->expected_date)
                    <div><dt class="text-terroir-dark/50">Livraison attendue</dt><dd class="mt-0.5 font-semibold">{{ $purchaseOrder->expected_date->format('d/m/Y') }}</dd></div>
                @endif
            </dl>

            @if($purchaseOrder->status === 'brouillon')
                <form action="{{ route('admin.bons-commande.status', $purchaseOrder) }}" method="POST" class="mt-4">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="envoyee">
                    <button type="submit" class="btn-primary w-full justify-center">Envoyer au fournisseur</button>
                </form>
            @endif

            @if($purchaseOrder->status === 'envoyee')
                <form action="{{ route('admin.bons-commande.status', $purchaseOrder) }}" method="POST" class="mt-4">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="confirmee">
                    <button type="submit" class="btn-primary w-full justify-center">Marquer confirmée</button>
                </form>
            @endif

            @if(!in_array($purchaseOrder->status, ['recue', 'annulee']))
                <form action="{{ route('admin.bons-commande.status', $purchaseOrder) }}" method="POST" class="mt-3" onsubmit="return confirm('Annuler ce bon de commande ?')">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="annulee">
                    <button type="submit" class="btn-outline w-full justify-center">Annuler le bon de commande</button>
                </form>
            @endif
        </div>

        <div class="admin-card mt-6">
            <h3 class="font-display text-base font-semibold">Paiement fournisseur</h3>
            <dl class="mt-3 space-y-2 text-sm">
                <div><dt class="text-terroir-dark/50">Total</dt><dd class="mt-0.5 font-semibold">{{ number_format($purchaseOrder->total, 0, ',', ' ') }} FCFA</dd></div>
                <div><dt class="text-terroir-dark/50">Payé</dt><dd class="mt-0.5 font-semibold text-terroir-green">{{ number_format($purchaseOrder->amount_paid, 0, ',', ' ') }} FCFA</dd></div>
                <div><dt class="text-terroir-dark/50">Solde dû</dt><dd class="mt-0.5 font-semibold text-terroir-terracotta">{{ number_format($purchaseOrder->balance(), 0, ',', ' ') }} FCFA</dd></div>
            </dl>

            @if($purchaseOrder->balance() > 0)
                <form action="{{ route('admin.fournisseurs.paiements.store', $purchaseOrder->supplier) }}" method="POST" class="mt-4 flex flex-col gap-2">
                    @csrf
                    <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">
                    <input type="number" step="0.01" name="amount" placeholder="Montant" max="{{ $purchaseOrder->balance() }}" required class="input">
                    <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required class="input">
                    <input type="text" name="method" placeholder="Mode de paiement" class="input">
                    <select name="payment_account_id" class="input">
                        <option value="">Compte de paiement (pour l'écriture comptable)</option>
                        @foreach(\App\Models\PaymentAccount::where('is_active', true)->get() as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary w-full justify-center">Enregistrer le paiement</button>
                </form>
            @endif

            @if($purchaseOrder->payments->isNotEmpty())
                <ul class="mt-4 space-y-1 border-t border-terroir-dark/10 pt-3 text-xs text-terroir-dark/60">
                    @foreach($purchaseOrder->payments as $payment)
                        <li class="flex justify-between"><span>{{ $payment->payment_date->format('d/m/Y') }}</span><span>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span></li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
