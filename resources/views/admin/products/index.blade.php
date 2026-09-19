@extends('layouts.admin')

@section('title', 'Produits')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="relative max-w-xs flex-1">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un produit..." class="input pl-9">
        <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-lg text-terroir-dark/40">search</span>
    </form>
    <a href="{{ route('admin.produits.create') }}" class="btn-primary">
        <span class="material-symbols-outlined text-lg">add</span>
        Nouveau produit
    </a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Produit</th>
                <th>Catégorie</th>
                <th class="text-right">Prix</th>
                <th class="text-right">Stock</th>
                <th>Statut</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $product->name }}</td>
                    <td class="text-terroir-dark/60">{{ $product->category?->name ?? '—' }}</td>
                    <td class="text-right">{{ number_format($product->price, 0, ',', ' ') }} FCFA</td>
                    <td class="text-right {{ $product->stock_quantity <= $product->stock_alert_threshold ? 'font-semibold text-terroir-terracotta' : '' }}">{{ $product->stock_quantity }}</td>
                    <td>
                        @if($product->is_active)
                            <span class="admin-badge-success">Actif</span>
                        @else
                            <span class="admin-badge-neutral">Inactif</span>
                        @endif
                    </td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.produits.edit', $product) }}" class="admin-link">Modifier</a>
                        <form action="{{ route('admin.produits.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce produit ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-8 text-center text-terroir-dark/40">Aucun produit.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $products->links() }}</div>
@endsection
