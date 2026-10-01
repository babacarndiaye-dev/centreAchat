@extends('layouts.app')

@section('title', $post->title." — DIABA HOTEL Produits du Sénégal (D.H.P.S)")

@section('content')
<article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
    <nav class="text-xs text-terroir-dark/50">
        <a href="{{ route('accueil') }}" class="hover:text-terroir-terracotta">Accueil</a> /
        <a href="{{ route('blog.index') }}" class="hover:text-terroir-terracotta">Actualités</a> /
        <span class="text-terroir-dark">{{ $post->title }}</span>
    </nav>

    <span class="section-eyebrow mt-6 inline-block">{{ ucfirst($post->type) }}</span>
    <h1 class="mt-2 font-display text-3xl font-semibold text-terroir-dark sm:text-4xl">{{ $post->title }}</h1>
    <p class="mt-3 text-sm text-terroir-dark/50">{{ optional($post->published_at)->format('d F Y') }}</p>

    @if($post->cover_image)
        <div class="mt-8 aspect-video overflow-hidden rounded-xl2 shadow-soft">
            <img src="{{ asset('fichiers/'.$post->cover_image) }}" alt="{{ $post->title }}" class="h-full w-full object-cover">
        </div>
    @endif

    <div class="prose prose-terroir mt-10 max-w-none leading-relaxed text-terroir-dark/80">
        {!! nl2br(e($post->content)) !!}
    </div>

    @if($related->isNotEmpty())
        <div class="mt-20 border-t border-terroir-green/10 pt-10">
            <h2 class="font-display text-xl font-semibold">À lire aussi</h2>
            <div class="mt-6 grid gap-6 sm:grid-cols-3">
                @foreach($related as $item)
                    <a href="{{ route('blog.show', $item->slug) }}" class="text-sm font-medium text-terroir-dark hover:text-terroir-terracotta">{{ $item->title }}</a>
                @endforeach
            </div>
        </div>
    @endif
</article>
@endsection
