@csrf

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="name">Nom du produit</label>
        <input type="text" id="name" name="name" value="{{ old('name', $product->name ?? '') }}" required class="input">
    </div>
    <div>
        <label class="label">Référence</label>
        @if(isset($product) && $product->reference)
            <input type="text" value="{{ $product->reference }}" disabled class="input bg-terroir-cream text-terroir-dark/60">
        @else
            <input type="text" value="Générée automatiquement à la création" disabled class="input bg-terroir-cream italic text-terroir-dark/40">
        @endif
    </div>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="category_id">Catégorie</label>
        <select id="category_id" name="category_id" required class="input">
            <option value="">Choisir...</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label" for="producer_id">Producteur (optionnel)</label>
        <select id="producer_id" name="producer_id" class="input">
            <option value="">Aucun</option>
            @foreach($producers as $producer)
                <option value="{{ $producer->id }}" @selected(old('producer_id', $product->producer_id ?? '') == $producer->id)>{{ $producer->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-6">
    <label class="label" for="short_description">Description courte</label>
    <input type="text" id="short_description" name="short_description" value="{{ old('short_description', $product->short_description ?? '') }}" class="input">
</div>

<div class="mt-6">
    <label class="label" for="description">Description complète</label>
    <textarea id="description" name="description" rows="5" class="input">{{ old('description', $product->description ?? '') }}</textarea>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2 md:grid-cols-4">
    <div>
        <label class="label" for="origin">Origine</label>
        <input type="text" id="origin" name="origin" value="{{ old('origin', $product->origin ?? '') }}" class="input">
    </div>
    <div>
        <label class="label" for="unit">Unité</label>
        <input type="text" id="unit" name="unit" list="units-datalist" value="{{ old('unit', $product->unit ?? 'kg') }}" required class="input">
        <datalist id="units-datalist">
            @foreach($units as $unitOption)
                <option value="{{ $unitOption->name }}">{{ $unitOption->abbreviation ? '('.$unitOption->abbreviation.')' : '' }}</option>
            @endforeach
        </datalist>
        <a href="{{ route('admin.produits-parametres.unites.index') }}" class="mt-2 block text-sm text-terroir-green">Gérer les unités</a>
    </div>
    <div>
        <label class="label" for="packaging_type_id">Conditionnement (optionnel)</label>
        <select id="packaging_type_id" name="packaging_type_id" class="input">
            <option value="">Aucun</option>
            @foreach($packagingTypes as $packaging)
                <option value="{{ $packaging->id }}" @selected(old('packaging_type_id', $product->packaging_type_id ?? '') == $packaging->id)>{{ $packaging->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label" for="weight">Poids (kg)</label>
        <input type="number" step="0.001" id="weight" name="weight" value="{{ old('weight', $product->weight ?? '') }}" class="input">
    </div>
</div>

@if($attributes->isNotEmpty())
    <div class="mt-6 border-t border-terroir-dark/10 pt-6">
        <h3 class="font-display text-base font-semibold">Attributs personnalisés</h3>
        <div class="mt-3 grid gap-4 sm:grid-cols-2">
            @foreach($attributes as $attribute)
                <div>
                    <label class="label" for="attribute-{{ $attribute->id }}">{{ $attribute->name }}</label>
                    <input type="text" id="attribute-{{ $attribute->id }}" name="attributes[{{ $attribute->id }}]" value="{{ old('attributes.'.$attribute->id, $attributeValues[$attribute->id] ?? '') }}" class="input">
                </div>
            @endforeach
        </div>
        <a href="{{ route('admin.produits-parametres.attributs.index') }}" class="mt-2 block text-sm text-terroir-green">Gérer les attributs</a>
    </div>
@endif

<div class="mt-6 border-t border-terroir-dark/10 pt-6">
    <h3 class="font-display text-base font-semibold">Tarification</h3>
    <div class="mt-3 grid gap-4 sm:grid-cols-2 md:grid-cols-4">
        <div>
            <label class="label" for="price">Prix public (FCFA)</label>
            <input type="number" step="0.01" id="price" name="price" value="{{ old('price', $product->price ?? '') }}" required class="input">
        </div>
        <div>
            <label class="label" for="professional_price">Prix professionnel</label>
            <input type="number" step="0.01" id="professional_price" name="professional_price" value="{{ old('professional_price', $product->professional_price ?? '') }}" class="input">
        </div>
        <div>
            <label class="label" for="wholesale_price">Prix en gros</label>
            <input type="number" step="0.01" id="wholesale_price" name="wholesale_price" value="{{ old('wholesale_price', $product->wholesale_price ?? '') }}" class="input">
        </div>
        <div>
            <label class="label" for="promo_price">Prix promotionnel</label>
            <input type="number" step="0.01" id="promo_price" name="promo_price" value="{{ old('promo_price', $product->promo_price ?? '') }}" class="input">
        </div>
        <div>
            <label class="label" for="promo_starts_at">Début promo</label>
            <input type="datetime-local" id="promo_starts_at" name="promo_starts_at" value="{{ old('promo_starts_at', optional($product->promo_starts_at ?? null)->format('Y-m-d\TH:i')) }}" class="input">
        </div>
        <div>
            <label class="label" for="promo_ends_at">Fin promo</label>
            <input type="datetime-local" id="promo_ends_at" name="promo_ends_at" value="{{ old('promo_ends_at', optional($product->promo_ends_at ?? null)->format('Y-m-d\TH:i')) }}" class="input">
        </div>
    </div>
</div>

<div class="mt-6 border-t border-terroir-dark/10 pt-6">
    <h3 class="font-display text-base font-semibold">Stock</h3>
    <div class="mt-3 grid gap-4 sm:grid-cols-3">
        <div>
            <label class="label" for="stock_quantity">Quantité en stock</label>
            <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" required class="input">
        </div>
        <div>
            <label class="label" for="stock_alert_threshold">Seuil d'alerte</label>
            <input type="number" id="stock_alert_threshold" name="stock_alert_threshold" value="{{ old('stock_alert_threshold', $product->stock_alert_threshold ?? 5) }}" required class="input">
        </div>
        <div>
            <label class="label" for="expiry_date">Date d'expiration</label>
            <input type="date" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', optional($product->expiry_date ?? null)->format('Y-m-d')) }}" class="input">
        </div>
    </div>
</div>

<div class="mt-6 border-t border-terroir-dark/10 pt-6">
    <h3 class="font-display text-base font-semibold">Images</h3>
    <div uk-form-custom="target: true" class="mt-3">
        <input type="file" name="images[]" multiple accept="image/*">
        <span class="input inline-flex items-center text-terroir-dark/50">Choisir des fichiers...</span>
    </div>

    @isset($product)
        @if($product->images->isNotEmpty())
            <div class="mt-3 flex flex-wrap gap-3">
                @foreach($product->images as $image)
                    <div class="relative">
                        <img src="{{ asset('fichiers/'.$image->path) }}" alt="" class="h-20 w-20 rounded-lg object-cover">
                        <button type="submit" form="delete-image-{{ $image->id }}" class="absolute -right-2 -top-2 flex h-[22px] w-[22px] items-center justify-center rounded-full bg-terroir-terracotta text-[11px] text-white" onclick="return confirm('Supprimer cette image ?')">✕</button>
                    </div>
                @endforeach
            </div>
        @endif
    @endisset
</div>

<div class="mt-6 flex flex-wrap gap-6 border-t border-terroir-dark/10 pt-6">
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false)) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
        Produit vedette
    </label>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_new" value="1" @checked(old('is_new', $product->is_new ?? false)) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
        Nouveauté
    </label>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
        Actif
    </label>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary">Enregistrer</button>
    <a href="{{ route('admin.produits.index') }}" class="btn-outline">Annuler</a>
</div>
