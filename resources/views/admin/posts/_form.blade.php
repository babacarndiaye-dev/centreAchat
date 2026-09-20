@csrf

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="title">Titre</label>
        <input type="text" id="title" name="title" value="{{ old('title', $post->title ?? '') }}" required class="input">
    </div>
    <div>
        <label class="label" for="type">Type</label>
        <select id="type" name="type" required class="input">
            @foreach(['actualite' => 'Actualité', 'recette' => 'Recette', 'blog' => 'Blog'] as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $post->type ?? 'actualite') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-6">
    <label class="label" for="excerpt">Résumé</label>
    <input type="text" id="excerpt" name="excerpt" value="{{ old('excerpt', $post->excerpt ?? '') }}" class="input">
</div>

<div class="mt-6">
    <label class="label" for="content">Contenu</label>
    <textarea id="content" name="content" rows="10" class="input">{{ old('content', $post->content ?? '') }}</textarea>
</div>

<div class="mt-6">
    <label class="label" for="cover_image">Image de couverture</label>
    <div uk-form-custom="target: true">
        <input type="file" id="cover_image" name="cover_image" accept="image/*">
        <span class="input inline-flex items-center text-terroir-dark/50">Choisir un fichier...</span>
    </div>
    @isset($post)
        @if($post->cover_image)
            <img src="{{ asset('fichiers/'.$post->cover_image) }}" alt="" class="mt-3" style="height:96px; width:160px; border-radius:10px; object-fit:cover;">
        @endif
    @endisset
</div>

<div class="mt-6">
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $post->is_published ?? true)) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
        Publié
    </label>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary">Enregistrer</button>
    <a href="{{ route('admin.articles.index') }}" class="btn-outline">Annuler</a>
</div>
