@extends('layouts.admin')

@section('title', 'Messagerie')

@section('content')
<div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap" style="gap:12px;">
    <div class="uk-flex uk-flex-wrap uk-text-small" style="gap:8px;">
        <a href="{{ route('admin.messagerie.index') }}" style="border-radius:999px; padding:6px 14px; {{ request('status') ? 'color:rgba(31,35,40,.6);' : 'background:#1D8A4E; color:#fff; font-weight:600;' }}">Toutes</a>
        @foreach(\App\Models\Conversation::STATUSES as $key => $label)
            <a href="{{ route('admin.messagerie.index', ['status' => $key]) }}" style="border-radius:999px; padding:6px 14px; {{ request('status') === $key ? 'background:#1D8A4E; color:#fff; font-weight:600;' : 'color:rgba(31,35,40,.6);' }}">{{ $label }}</a>
        @endforeach
    </div>
    <a href="{{ route('admin.messagerie.faq.index') }}" style="font-weight:600; color:#1D8A4E;">📚 Base de connaissances</a>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="padding:0;">
    @forelse($conversations as $conversation)
        <a href="{{ route('admin.messagerie.show', $conversation) }}" class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap" style="gap:16px; padding:16px 20px; border-bottom:1px solid rgba(31,35,40,.08); {{ $conversation->unread_count > 0 ? 'background:rgba(29,138,78,.05);' : '' }}">
            <div style="min-width:0;">
                <p style="font-weight:600;">
                    {{ $conversation->customerName() }}
                    <span class="uk-label" style="background:#F7F8F5; color:rgba(31,35,40,.6); margin-left:8px;">{{ \App\Models\Conversation::STATUSES[$conversation->status] ?? $conversation->status }}</span>
                    @if($conversation->assignee)
                        <span class="uk-text-small" style="color:rgba(31,35,40,.4);">— {{ $conversation->assignee->name }}</span>
                    @endif
                </p>
                <p class="uk-margin-small-top uk-text-small uk-text-muted" style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ optional($conversation->latestMessage)->body }}</p>
            </div>
            <div class="uk-flex uk-flex-middle" style="gap:12px; flex-shrink:0;">
                @if($conversation->unread_count > 0)
                    <span class="uk-label" style="background:#E8604F; color:#fff; border-radius:999px; min-width:20px; height:20px; display:inline-flex; align-items:center; justify-content:center; padding:0 6px;">{{ $conversation->unread_count }}</span>
                @endif
                <span class="uk-text-small" style="color:rgba(31,35,40,.4);">{{ optional($conversation->last_message_at)->diffForHumans() }}</span>
            </div>
        </a>
    @empty
        <p class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucune conversation.</p>
    @endforelse
</div>

<div class="uk-margin-top">
    {{ $conversations->links() }}
</div>
@endsection
