@extends('layouts.admin')

@section('title', $supplier->name)

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4">
    <div>
        <h2 class="font-display text-2xl font-semibold">{{ $supplier->name }}</h2>
        <p class="text-sm text-terroir-dark/50">{{ $supplier->company_name }}</p>
    </div>
    <a href="{{ route('admin.fournisseurs.edit', $supplier) }}" class="btn-outline">Modifier la fiche</a>
</div>

<div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
    <div class="admin-card">
        <p class="text-xs text-terroir-dark/50">Total commandé</p>
        <p class="mt-1.5 text-xl font-bold">{{ number_format($supplier->totalOwed(), 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="admin-card">
        <p class="text-xs text-terroir-dark/50">Total payé</p>
        <p class="mt-1.5 text-xl font-bold text-terroir-green">{{ number_format($supplier->totalPaid(), 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="admin-card">
        <p class="text-xs text-terroir-dark/50">Solde dû</p>
        <p class="mt-1.5 text-xl font-bold text-terroir-terracotta">{{ number_format($supplier->balance(), 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="admin-card">
        <p class="text-xs text-terroir-dark/50">Statut</p>
        <p class="mt-1.5 text-xl font-bold">{{ \App\Models\Supplier::STATUSES[$supplier->status] }}</p>
    </div>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="admin-card">
        <dl class="space-y-2 text-sm">
            <div><dt class="text-terroir-dark/50">Responsable</dt><dd class="mt-0.5 font-semibold">{{ $supplier->contact_name ?? '—' }}</dd></div>
            <div><dt class="text-terroir-dark/50">Téléphone</dt><dd class="mt-0.5 font-semibold">{{ $supplier->phone }}</dd></div>
            <div><dt class="text-terroir-dark/50">E-mail</dt><dd class="mt-0.5 font-semibold">{{ $supplier->email ?? '—' }}</dd></div>
            <div><dt class="text-terroir-dark/50">Adresse</dt><dd class="mt-0.5 font-semibold">{{ $supplier->address ?? '—' }}, {{ $supplier->city }} {{ $supplier->region }}</dd></div>
            <div><dt class="text-terroir-dark/50">Conditions de paiement</dt><dd class="mt-0.5 font-semibold">{{ $supplier->payment_terms ?? '—' }}</dd></div>
            <div><dt class="text-terroir-dark/50">Délai de livraison</dt><dd class="mt-0.5 font-semibold">{{ $supplier->delivery_delay_days ? $supplier->delivery_delay_days.' jours' : '—' }}</dd></div>
            @if($supplier->notes)
                <div><dt class="text-terroir-dark/50">Notes</dt><dd class="mt-0.5 font-semibold">{{ $supplier->notes }}</dd></div>
            @endif
        </dl>
    </div>

    <div class="admin-card">
        <h3 class="font-display text-base font-semibold">Accès au portail fournisseur</h3>
        @if($supplier->user)
            <p class="mt-1.5 text-sm text-terroir-dark/70">Accès actif pour <strong>{{ $supplier->user->email }}</strong>.</p>
            <form action="{{ route('admin.fournisseurs.acces.destroy', $supplier) }}" method="POST" class="mt-4" onsubmit="return confirm('Révoquer l\'accès portail de ce fournisseur ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-outline">Révoquer l'accès</button>
            </form>
        @else
            <p class="mt-1.5 text-sm text-terroir-dark/50">Ce fournisseur n'a pas encore d'accès au portail. Créez un compte pour lui permettre de consulter ses commandes et confirmer leur disponibilité.</p>
            <form action="{{ route('admin.fournisseurs.acces.store', $supplier) }}" method="POST" class="mt-4 flex flex-wrap gap-3">
                @csrf
                <input type="email" name="email" placeholder="E-mail du fournisseur" value="{{ $supplier->email }}" required class="input max-w-xs">
                <button type="submit" class="btn-primary">Créer l'accès</button>
            </form>
        @endif
    </div>

    <div class="admin-card">
        <h3 class="font-display text-base font-semibold">Enregistrer un paiement</h3>
        <form action="{{ route('admin.fournisseurs.paiements.store', $supplier) }}" method="POST" class="mt-4 flex flex-col gap-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <input type="number" step="0.01" name="amount" placeholder="Montant (FCFA)" required class="input">
                <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required class="input">
            </div>
            <select name="purchase_order_id" class="input">
                <option value="">Sans bon de commande lié</option>
                @foreach($supplier->purchaseOrders as $po)
                    <option value="{{ $po->id }}">{{ $po->order_number }} — solde {{ number_format($po->balance(), 0, ',', ' ') }} FCFA</option>
                @endforeach
            </select>
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="method" placeholder="Mode (Espèces, Wave...)" class="input">
                <input type="text" name="reference" placeholder="Référence" class="input">
            </div>
            <select name="payment_account_id" class="input">
                <option value="">Compte de paiement (pour l'écriture comptable)</option>
                @foreach(\App\Models\PaymentAccount::where('is_active', true)->get() as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary w-full justify-center">Enregistrer le paiement</button>
        </form>
    </div>
</div>

<div class="admin-card mt-6">
    <h3 class="font-display text-base font-semibold">Catalogue produits du fournisseur</h3>
    <form action="{{ route('admin.fournisseurs.produits.store', $supplier) }}" method="POST" class="mt-4 flex flex-wrap gap-3">
        @csrf
        <select name="product_id" required class="input min-w-[220px] flex-1">
            <option value="">Choisir un produit...</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
        </select>
        <input type="number" step="0.01" name="supplier_price" placeholder="Prix d'achat" required class="input w-40">
        <input type="text" name="supplier_reference" placeholder="Réf. fournisseur" class="input w-40">
        <button type="submit" class="btn-primary">Ajouter</button>
    </form>

    <div class="mt-4 overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="text-right">Prix d'achat</th>
                    <th>Réf. fournisseur</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplier->supplierProducts as $sp)
                    <tr>
                        <td>{{ $sp->product->name }}</td>
                        <td class="text-right">{{ number_format($sp->supplier_price, 0, ',', ' ') }} FCFA</td>
                        <td>{{ $sp->supplier_reference ?? '—' }}</td>
                        <td class="text-right">
                            <form action="{{ route('admin.fournisseurs.produits.destroy', $sp) }}" method="POST" onsubmit="return confirm('Retirer ce produit du catalogue ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-link-danger bg-transparent">Retirer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-6 text-center text-terroir-dark/40">Aucun produit référencé pour ce fournisseur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="admin-card mt-6">
    <div class="flex items-center justify-between">
        <h3 class="font-display text-base font-semibold">Bons de commande</h3>
        <a href="{{ route('admin.bons-commande.create') }}" class="admin-link">+ Nouveau bon de commande</a>
    </div>
    <div class="mt-4 overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Solde</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplier->purchaseOrders as $po)
                    <tr>
                        <td><a href="{{ route('admin.bons-commande.show', $po) }}" class="admin-link">{{ $po->order_number }}</a></td>
                        <td class="text-terroir-dark/60">{{ $po->order_date->format('d/m/Y') }}</td>
                        <td>{{ \App\Models\PurchaseOrder::STATUSES[$po->status] }}</td>
                        <td class="text-right">{{ number_format($po->total, 0, ',', ' ') }} FCFA</td>
                        <td class="text-right {{ $po->balance() > 0 ? 'font-semibold text-terroir-terracotta' : '' }}">{{ number_format($po->balance(), 0, ',', ' ') }} FCFA</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-terroir-dark/40">Aucun bon de commande pour ce fournisseur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
