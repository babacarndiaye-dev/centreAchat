@extends('layouts.app')

@section('title', 'Conversation')

@section('content')
<section class="mx-auto max-w-2xl px-4 py-16 sm:px-6 lg:px-8">
    <span class="section-eyebrow">Espace client</span>
    <h1 class="section-title mt-2">Conversation du {{ $conversation->created_at->format('d/m/Y') }}</h1>

    <a href="{{ route('compte.messages.index') }}" class="mt-4 inline-block text-sm text-terroir-dark/60 hover:text-terroir-terracotta">← Retour à mes conversations</a>

    <div class="card mt-8 space-y-3 p-6">
        @foreach($conversation->messages as $message)
            <div class="flex {{ $message->sender_type === 'client' ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[80%] rounded-2xl px-4 py-2.5 text-sm {{ $message->sender_type === 'client' ? 'bg-terroir-green text-white' : ($message->sender_type === 'bot' ? 'bg-terroir-gold/15 text-terroir-dark' : 'bg-terroir-cream text-terroir-dark') }}">
                    @if($message->sender_type === 'bot')
                        <p class="mb-0.5 text-[10px] font-semibold uppercase tracking-wide opacity-60">🤖 Assistant</p>
                    @endif
                    <p class="whitespace-pre-line">{{ $message->body }}</p>
                    @foreach($message->meta['links'] ?? [] as $link)
                        <a href="{{ $link['url'] }}" class="mt-1 block truncate text-xs font-medium underline opacity-80 hover:opacity-100">{{ $link['label'] }}</a>
                    @endforeach
                    <p class="mt-1 text-[10px] opacity-60">{{ $message->created_at->format('d/m H:i') }}</p>
                </div>
            </div>
        @endforeach
    </div>

    @if($conversation->status !== 'fermee')
        <p class="mt-4 text-sm text-terroir-dark/50">Cette conversation est toujours ouverte — utilisez le chat en bas à droite pour continuer à échanger.</p>
    @endif
</section>
@endsection
