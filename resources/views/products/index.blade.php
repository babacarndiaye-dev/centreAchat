@extends('layouts.app')

@section('title', "Nos produits — Central d'Achat")

@section('content')
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">

    <div class="reveal is-visible text-center">
        <span class="section-eyebrow">Catalogue</span>
        <h1 class="section-title mt-2">Nos produits du terroir</h1>
        <p class="mx-auto mt-3 max-w-xl text-terroir-dark/70">Une sélection rigoureuse de produits locaux, frais et authentiques.</p>
    </div>

    <form method="GET" action="{{ route('produits.index') }}" class="mt-10 flex flex-wrap items-center justify-center gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un produit..." class="input max-w-xs">

        <select name="categorie" class="input max-w-[200px]" onchange="this.form.submit()">
            <option value="">Toutes catégories</option>
            @foreach($categories as $category)
                <option value="{{ $category->slug }}" @selected(request('categorie') === $category->slug)>{{ $category->name }}</option>
            @endforeach
        </select>

        <select name="tri" class="input max-w-[200px]" onchange="this.form.submit()">
            <option value="recent" @selected(request('tri', 'recent') === 'recent')>Plus récents</option>
            <option value="prix_asc" @selected(request('tri') === 'prix_asc')>Prix croissant</option>
            <option value="prix_desc" @selected(request('tri') === 'prix_desc')>Prix décroissant</option>
            <option value="nom" @selected(request('tri') === 'nom')>Nom (A-Z)</option>
        </select>

        <button type="submit" class="btn-primary">Filtrer</button>
    </form>

    @if($products->isEmpty())
        <div class="mt-16 text-center text-terroir-dark/60">
            <p class="text-5xl">🌱</p>
            <p class="mt-4">Aucun produit ne correspond à votre recherche pour le moment.</p>
        </div>
    @else
        <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($products as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>

        <div class="mt-12">
            {{ $products->links() }}
        </div>
    @endif

</section>
@endsection
