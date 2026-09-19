@csrf

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="name">Nom</label>
        <input type="text" id="name" name="name" value="{{ old('name', $producer->name ?? '') }}" required class="input">
    </div>
    <div>
        <label class="label" for="region">Région</label>
        <input type="text" id="region" name="region" value="{{ old('region', $producer->region ?? '') }}" class="input">
    </div>
</div>

<div class="mt-6">
    <label class="label" for="description">Description</label>
    <textarea id="description" name="description" rows="4" class="input">{{ old('description', $producer->description ?? '') }}</textarea>
</div>

<div class="mt-6">
    <label class="label" for="photo">Photo</label>
    <div uk-form-custom="target: true">
        <input type="file" id="photo" name="photo" accept="image/*">
        <span class="input" style="display:inline-flex; align-items:center; color:rgba(31,35,40,.5);">Choisir un fichier...</span>
    </div>
    @isset($producer)
        @if($producer->photo)
            <img src="{{ asset('fichiers/'.$producer->photo) }}" alt="" class="mt-3" style="height:80px; width:80px; border-radius:10px; object-fit:cover;">
        @endif
    @endisset
</div>

<div class="mt-6 flex gap-6">
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $producer->is_featured ?? false)) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
        Producteur vedette
    </label>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $producer->is_active ?? true)) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
        Actif
    </label>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary">Enregistrer</button>
    <a href="{{ route('admin.producteurs.index') }}" class="btn-outline">Annuler</a>
</div>
