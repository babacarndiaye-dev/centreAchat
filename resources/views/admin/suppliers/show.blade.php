@extends('layouts.admin')

@section('title', $supplier->name)

@section('content')
<div class="uk-flex uk-flex-wrap uk-flex-top uk-flex-between" style="gap:16px;">
    <div>
        <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.5rem;">{{ $supplier->name }}</h2>
        <p class="uk-text-small uk-text-muted">{{ $supplier->company_name }}</p>
    </div>
    <a href="{{ route('admin.fournisseurs.edit', $supplier) }}" class="uk-button uk-button-default">Modifier la fiche</a>
</div>

<div class="uk-grid-small uk-child-width-1-2 uk-child-width-1-4@s uk-margin-top" uk-grid>
    <div>
        <div class="uk-card uk-card-default" style="padding:20px;">
            <p style="font-size:.75rem; color:rgba(31,35,40,.5);">Total commandé</p>
            <p class="uk-margin-small-top" style="font-size:1.25rem; font-weight:700;">{{ number_format($supplier->totalOwed(), 0, ',', ' ') }} FCFA</p>
        </div>
    </div>
    <div>
        <div class="uk-card uk-card-default" style="padding:20px;">
            <p style="font-size:.75rem; color:rgba(31,35,40,.5);">Total payé</p>
            <p class="uk-margin-small-top" style="font-size:1.25rem; font-weight:700; color:#1D8A4E;">{{ number_format($supplier->totalPaid(), 0, ',', ' ') }} FCFA</p>
        </div>
    </div>
    <div>
        <div class="uk-card uk-card-default" style="padding:20px;">
            <p style="font-size:.75rem; color:rgba(31,35,40,.5);">Solde dû</p>
            <p class="uk-margin-small-top" style="font-size:1.25rem; font-weight:700; color:#E8604F;">{{ number_format($supplier->balance(), 0, ',', ' ') }} FCFA</p>
        </div>
    </div>
    <div>
        <div class="uk-card uk-card-default" style="padding:20px;">
            <p style="font-size:.75rem; color:rgba(31,35,40,.5);">Statut</p>
            <p class="uk-margin-small-top" style="font-size:1.25rem; font-weight:700;">{{ \App\Models\Supplier::STATUSES[$supplier->status] }}</p>
        </div>
    </div>
</div>

<div class="uk-grid-small uk-child-width-1-2@l uk-margin-top" uk-grid>
    <div>
        <div class="uk-card uk-card-default" style="padding:24px;">
            <dl style="font-size:.875rem;">
                <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Responsable</dt><dd style="font-weight:600; margin-top:2px;">{{ $supplier->contact_name ?? '—' }}</dd></div>
                <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Téléphone</dt><dd style="font-weight:600; margin-top:2px;">{{ $supplier->phone }}</dd></div>
                <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">E-mail</dt><dd style="font-weight:600; margin-top:2px;">{{ $supplier->email ?? '—' }}</dd></div>
                <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Adresse</dt><dd style="font-weight:600; margin-top:2px;">{{ $supplier->address ?? '—' }}, {{ $supplier->city }} {{ $supplier->region }}</dd></div>
                <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Conditions de paiement</dt><dd style="font-weight:600; margin-top:2px;">{{ $supplier->payment_terms ?? '—' }}</dd></div>
                <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Délai de livraison</dt><dd style="font-weight:600; margin-top:2px;">{{ $supplier->delivery_delay_days ? $supplier->delivery_delay_days.' jours' : '—' }}</dd></div>
                @if($supplier->notes)
                    <div class="uk-margin-small-bottom"><dt style="color:rgba(31,35,40,.5);">Notes</dt><dd style="font-weight:600; margin-top:2px;">{{ $supplier->notes }}</dd></div>
                @endif
            </dl>
        </div>
    </div>

    <div>
        <div class="uk-card uk-card-default" style="padding:24px;">
            <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Accès au portail fournisseur</h3>
            @if($supplier->user)
                <p class="uk-margin-small-top" style="font-size:.875rem; color:rgba(31,35,40,.7);">Accès actif pour <strong>{{ $supplier->user->email }}</strong>.</p>
                <form action="{{ route('admin.fournisseurs.acces.destroy', $supplier) }}" method="POST" class="uk-margin-top" onsubmit="return confirm('Révoquer l\'accès portail de ce fournisseur ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="uk-button uk-button-default">Révoquer l'accès</button>
                </form>
            @else
                <p class="uk-margin-small-top uk-text-muted" style="font-size:.875rem;">Ce fournisseur n'a pas encore d'accès au portail. Créez un compte pour lui permettre de consulter ses commandes et confirmer leur disponibilité.</p>
                <form action="{{ route('admin.fournisseurs.acces.store', $supplier) }}" method="POST" class="uk-flex uk-flex-wrap uk-margin-top" style="gap:12px;">
                    @csrf
                    <input type="email" name="email" placeholder="E-mail du fournisseur" value="{{ $supplier->email }}" required class="uk-input" style="max-width:20rem;">
                    <button type="submit" class="uk-button uk-button-primary">Créer l'accès</button>
                </form>
            @endif
        </div>
    </div>

    <div>
        <div class="uk-card uk-card-default" style="padding:24px;">
            <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Enregistrer un paiement</h3>
            <form action="{{ route('admin.fournisseurs.paiements.store', $supplier) }}" method="POST" class="uk-margin-top" style="display:flex; flex-direction:column; gap:12px;">
                @csrf
                <div class="uk-grid-small uk-child-width-1-2" uk-grid>
                    <div><input type="number" step="0.01" name="amount" placeholder="Montant (FCFA)" required class="uk-input"></div>
                    <div><input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required class="uk-input"></div>
                </div>
                <select name="purchase_order_id" class="uk-select">
                    <option value="">Sans bon de commande lié</option>
                    @foreach($supplier->purchaseOrders as $po)
                        <option value="{{ $po->id }}">{{ $po->order_number }} — solde {{ number_format($po->balance(), 0, ',', ' ') }} FCFA</option>
                    @endforeach
                </select>
                <div class="uk-grid-small uk-child-width-1-2" uk-grid>
                    <div><input type="text" name="method" placeholder="Mode (Espèces, Wave...)" class="uk-input"></div>
                    <div><input type="text" name="reference" placeholder="Référence" class="uk-input"></div>
                </div>
                <select name="payment_account_id" class="uk-select">
                    <option value="">Compte de paiement (pour l'écriture comptable)</option>
                    @foreach(\App\Models\PaymentAccount::where('is_active', true)->get() as $account)
                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="uk-button uk-button-primary uk-width-1-1">Enregistrer le paiement</button>
            </form>
        </div>
    </div>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="padding:24px;">
    <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Catalogue produits du fournisseur</h3>
    <form action="{{ route('admin.fournisseurs.produits.store', $supplier) }}" method="POST" class="uk-grid-small uk-margin-top" uk-grid>
        @csrf
        <div class="uk-width-1-1 uk-width-2-5@s">
            <select name="product_id" required class="uk-select">
                <option value="">Choisir un produit...</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="uk-width-1-2 uk-width-1-5@s"><input type="number" step="0.01" name="supplier_price" placeholder="Prix d'achat" required class="uk-input"></div>
        <div class="uk-width-1-2 uk-width-1-5@s"><input type="text" name="supplier_reference" placeholder="Réf. fournisseur" class="uk-input"></div>
        <div class="uk-width-1-1 uk-width-1-5@s"><button type="submit" class="uk-button uk-button-primary">Ajouter</button></div>
    </form>

    <div class="uk-margin-top" style="overflow-x:auto;">
        <table class="uk-table uk-table-divider uk-table-middle">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="uk-text-right">Prix d'achat</th>
                    <th>Réf. fournisseur</th>
                    <th class="uk-text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplier->supplierProducts as $sp)
                    <tr>
                        <td>{{ $sp->product->name }}</td>
                        <td class="uk-text-right">{{ number_format($sp->supplier_price, 0, ',', ' ') }} FCFA</td>
                        <td>{{ $sp->supplier_reference ?? '—' }}</td>
                        <td class="uk-text-right">
                            <form action="{{ route('admin.fournisseurs.produits.destroy', $sp) }}" method="POST" onsubmit="return confirm('Retirer ce produit du catalogue ?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Retirer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="uk-text-center uk-text-muted" style="padding:24px 0;">Aucun produit référencé pour ce fournisseur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="padding:24px;">
    <div class="uk-flex uk-flex-middle uk-flex-between">
        <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Bons de commande</h3>
        <a href="{{ route('admin.bons-commande.create') }}" style="font-size:.875rem; font-weight:600; color:#1D8A4E;">+ Nouveau bon de commande</a>
    </div>
    <div class="uk-margin-top" style="overflow-x:auto;">
        <table class="uk-table uk-table-divider uk-table-middle">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th class="uk-text-right">Total</th>
                    <th class="uk-text-right">Solde</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplier->purchaseOrders as $po)
                    <tr>
                        <td><a href="{{ route('admin.bons-commande.show', $po) }}" style="font-weight:600; color:#1D8A4E;">{{ $po->order_number }}</a></td>
                        <td class="uk-text-muted">{{ $po->order_date->format('d/m/Y') }}</td>
                        <td>{{ \App\Models\PurchaseOrder::STATUSES[$po->status] }}</td>
                        <td class="uk-text-right">{{ number_format($po->total, 0, ',', ' ') }} FCFA</td>
                        <td class="uk-text-right" style="{{ $po->balance() > 0 ? 'color:#E8604F; font-weight:600;' : '' }}">{{ number_format($po->balance(), 0, ',', ' ') }} FCFA</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="uk-text-center uk-text-muted" style="padding:24px 0;">Aucun bon de commande pour ce fournisseur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
