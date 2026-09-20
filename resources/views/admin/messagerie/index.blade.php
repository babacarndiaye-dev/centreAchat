@extends('layouts.admin')

@section('title', 'Messagerie')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div class="flex flex-wrap gap-2 text-sm">
        <a href="{{ route('admin.messagerie.index') }}" class="rounded-full px-3.5 py-1.5 {{ request('status') ? 'text-terroir-dark/60' : 'bg-terroir-green font-semibold text-white' }}">Toutes</a>
        @foreach(\App\Models\Conversation::STATUSES as $key => $label)
            <a href="{{ route('admin.messagerie.index', ['status' => $key]) }}" class="rounded-full px-3.5 py-1.5 {{ request('status') === $key ? 'bg-terroir-green font-semibold text-white' : 'text-terroir-dark/60' }}">{{ $label }}</a>
        @endforeach
    </div>
    <a href="{{ route('admin.messagerie.faq.index') }}" class="admin-link">📚 Base de connaissances</a>
</div>

<div class="admin-card mt-6 p-0">
    @forelse($conversations as $conversation)
        <a
            href="{{ route('admin.messagerie.show', $conversation) }}"
            class="flex flex-wrap items-center justify-between gap-4 border-b border-terroir-dark/[0.08] px-5 py-4 {{ $conversation->unread_count > 0 ? 'bg-terroir-green/5' : '' }}"
        >
            <div class="min-w-0">
                <p class="font-semibold">
                    {{ $conversation->customerName() }}
                    <span class="admin-badge-neutral ml-2">{{ \App\Models\Conversation::STATUSES[$conversation->status] ?? $conversation->status }}</span>
                    @if($conversation->assignee)
                        <span class="text-sm text-terroir-dark/40">— {{ $conversation->assignee->name }}</span>
                    @endif
                </p>
                <p class="mt-1.5 truncate text-sm text-terroir-dark/50">{{ optional($conversation->latestMessage)->body }}</p>
            </div>
            <div class="flex shrink-0 items-center gap-3">
                @if($conversation->unread_count > 0)
                    <span class="inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-terroir-terracotta px-1.5 text-xs font-semibold text-white">{{ $conversation->unread_count }}</span>
                @endif
                <span class="text-sm text-terroir-dark/40">{{ optional($conversation->last_message_at)->diffForHumans() }}</span>
            </div>
        </a>
    @empty
        <p class="py-8 text-center text-terroir-dark/40">Aucune conversation.</p>
    @endforelse
</div>

<div class="mt-6">
    {{ $conversations->links() }}
</div>
@endsection
