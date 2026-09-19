@extends('layouts.app')

@section('title', 'Mes conversations')

@section('content')
<section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
    <span class="section-eyebrow">Espace client</span>
    <h1 class="section-title mt-2">Mes conversations</h1>

    <a href="{{ route('compte.index') }}" class="mt-4 inline-block text-sm text-terroir-dark/60 hover:text-terroir-terracotta">← Retour à mon compte</a>

    @if($conversations->isEmpty())
        <p class="mt-10 text-terroir-dark/60">Vous n'avez pas encore échangé avec nous via le chat.</p>
    @else
        <div class="mt-8 space-y-3">
            @foreach($conversations as $conversation)
                <a href="{{ route('compte.messages.show', $conversation) }}" class="card block p-5 hover:border-terroir-green/30">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="text-sm text-terroir-dark/70">{{ optional($conversation->latestMessage)->body ?? 'Conversation vide' }}</p>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $conversation->status === 'fermee' ? 'bg-terroir-dark/10 text-terroir-dark/60' : 'bg-terroir-green/10 text-terroir-green' }}">{{ \App\Models\Conversation::STATUSES[$conversation->status] ?? $conversation->status }}</span>
                    </div>
                    <p class="mt-2 text-xs text-terroir-dark/40">{{ optional($conversation->last_message_at)->format('d/m/Y à H:i') }}</p>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $conversations->links() }}</div>
    @endif
</section>
@endsection
