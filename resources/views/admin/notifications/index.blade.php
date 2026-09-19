@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
<div class="uk-flex uk-flex-middle uk-flex-between">
    <p class="uk-text-small uk-text-muted">Notifications internes qui vous concernent.</p>
    <form action="{{ route('admin.notifications.read-all') }}" method="POST">
        @csrf @method('PATCH')
        <button type="submit" style="font-size:.875rem; font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;">Tout marquer comme lu</button>
    </form>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="padding:0;">
    @forelse($notifications as $notification)
        <div class="uk-flex uk-flex-between" style="gap:16px; padding:16px 20px; {{ $loop->last ? '' : 'border-bottom:1px solid rgba(31,35,40,.08);' }} {{ $notification->read_at ? '' : 'background:rgba(29,138,78,.05);' }}">
            <div>
                <p style="font-weight:600;">{{ $notification->title }}</p>
                <p class="uk-text-small uk-text-muted uk-margin-small-top">{{ $notification->body }}</p>
                <p style="font-size:.75rem; color:rgba(31,35,40,.4); margin-top:8px;">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            @unless($notification->read_at)
                <form action="{{ route('admin.notifications.read', $notification) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" style="white-space:nowrap; font-size:.75rem; font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;">Marquer comme lu</button>
                </form>
            @endunless
        </div>
    @empty
        <p class="uk-text-center uk-text-muted" style="padding:32px 20px;">Aucune notification pour le moment.</p>
    @endforelse
</div>

<div class="uk-margin-top">
    {{ $notifications->links() }}
</div>
@endsection
