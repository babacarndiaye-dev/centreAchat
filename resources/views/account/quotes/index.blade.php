@extends('layouts.app')

@section('title', 'Mes devis')

@section('content')
<section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="section-eyebrow">Espace professionnel</span>
            <h1 class="section-title mt-2">Mes devis</h1>
        </div>
        <a href="{{ route('compte.devis.create') }}" class="btn-primary">+ Demander un devis</a>
    </div>

    <a href="{{ route('compte.index') }}" class="mt-4 inline-block text-sm text-terroir-dark/60 hover:text-terroir-terracotta">← Retour à mon compte</a>

    @if($quotes->isEmpty())
        <p class="mt-10 text-terroir-dark/60">Vous n'avez pas encore de devis.</p>
    @else
        <div class="mt-8 space-y-4">
            @foreach($quotes as $quote)
                <a href="{{ route('compte.devis.show', $quote) }}" class="card flex flex-wrap items-center justify-between gap-4 p-5 transition hover:-translate-y-0.5">
                    <div>
                        <p class="font-semibold">{{ $quote->quote_number }}</p>
                        <p class="text-sm text-terroir-dark/50">{{ $quote->created_at->format('d/m/Y') }}</p>
                    </div>
                    <span class="rounded-full bg-terroir-cream px-4 py-1.5 text-xs font-semibold text-terroir-green">{{ \App\Models\Quote::STATUSES[$quote->status] }}</span>
                    @if($quote->total > 0)
                        <span class="font-bold text-terroir-green">{{ number_format($quote->total, 0, ',', ' ') }} FCFA</span>
                    @endif
                </a>
            @endforeach
        </div>
        <div class="mt-8">{{ $quotes->links() }}</div>
    @endif
</section>
@endsection
