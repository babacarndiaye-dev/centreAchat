@csrf

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="title">Titre</label>
        <input type="text" id="title" name="title" value="{{ old('title', $page->title ?? '') }}" required class="input">
    </div>
    <div>
        <label class="label" for="slug">Slug (URL)</label>
        <input type="text" id="slug" name="slug" value="{{ old('slug', $page->slug ?? '') }}" placeholder="genere-automatiquement-si-vide" class="input">
    </div>
</div>

<div class="mt-6">
    <label class="label" for="content">Contenu</label>
    <textarea id="content" name="content" rows="10" class="input">{{ old('content', $page->content ?? '') }}</textarea>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="label" for="meta_title">Titre SEO (optionnel)</label>
        <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $page->meta_title ?? '') }}" class="input">
    </div>
    <div>
        <label class="label" for="meta_description">Meta description (optionnel)</label>
        <input type="text" id="meta_description" name="meta_description" value="{{ old('meta_description', $page->meta_description ?? '') }}" class="input">
    </div>
</div>

<div class="mt-6">
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->is_published ?? true)) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
        Page publiée
    </label>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary">Enregistrer</button>
    <a href="{{ route('admin.pages.index') }}" class="btn-outline">Annuler</a>
</div>
