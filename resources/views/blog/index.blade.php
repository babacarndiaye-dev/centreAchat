@extends('layouts.app')

@section('title', "Actualités & recettes — DIABA HOTEL Produits du Sénégal (D.H.P.S)")

@section('content')
<section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="text-center">
        <span class="section-eyebrow">Le journal</span>
        <h1 class="section-title mt-2">Actualités, recettes &amp; blog</h1>
    </div>

    <div class="mt-8 flex flex-wrap justify-center gap-3">
        @foreach(['' => 'Tout', 'actualite' => 'Actualités', 'recette' => 'Recettes', 'blog' => 'Blog'] as $value => $label)
            <a href="{{ route('blog.index', $value ? ['type' => $value] : []) }}" class="rounded-full px-5 py-2 text-sm font-medium transition {{ request('type', '') === $value ? 'bg-terroir-green text-white' : 'bg-white text-terroir-dark/70 hover:bg-terroir-cream' }}">{{ $label }}</a>
        @endforeach
    </div>

    @if($posts->isEmpty())
        <p class="mt-16 text-center text-terroir-dark/60">Aucun article publié pour le moment.</p>
    @else
        <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($posts as $post)
                <a href="{{ route('blog.show', $post->slug) }}" class="card group overflow-hidden transition hover:-translate-y-1">
                    <div class="flex h-48 items-center justify-center overflow-hidden bg-terroir-cream text-5xl">
                        @if($post->cover_image)
                            <img src="{{ asset('fichiers/'.$post->cover_image) }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                        @else
                            📰
                        @endif
                    </div>
                    <div class="p-6">
                        <span class="text-xs font-semibold uppercase tracking-wide text-terroir-terracotta">{{ ucfirst($post->type) }}</span>
                        <h3 class="mt-2 font-display text-lg font-semibold line-clamp-2">{{ $post->title }}</h3>
                        <p class="mt-2 text-sm text-terroir-dark/60 line-clamp-2">{{ $post->excerpt }}</p>
                        <p class="mt-4 text-xs text-terroir-dark/40">{{ optional($post->published_at)->format('d/m/Y') }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-12">{{ $posts->links() }}</div>
    @endif
</section>
@endsection
