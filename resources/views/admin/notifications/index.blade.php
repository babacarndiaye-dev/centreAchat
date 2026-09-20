@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-sm text-terroir-dark/50">Notifications internes qui vous concernent.</p>
    <form action="{{ route('admin.notifications.read-all') }}" method="POST">
        @csrf @method('PATCH')
        <button type="submit" class="text-sm font-semibold text-terroir-green">Tout marquer comme lu</button>
    </form>
</div>

<div class="admin-card mt-6 p-0">
    @forelse($notifications as $notification)
        <div class="flex justify-between gap-4 px-5 py-4 {{ $loop->last ? '' : 'border-b border-terroir-dark/[0.08]' }} {{ $notification->read_at ? '' : 'bg-terroir-green/5' }}">
            <div>
                <p class="font-semibold">{{ $notification->title }}</p>
                <p class="mt-1.5 text-sm text-terroir-dark/50">{{ $notification->body }}</p>
                <p class="mt-2 text-xs text-terroir-dark/40">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            @unless($notification->read_at)
                <form action="{{ route('admin.notifications.read', $notification) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="whitespace-nowrap text-xs font-semibold text-terroir-green">Marquer comme lu</button>
                </form>
            @endunless
        </div>
    @empty
        <p class="py-8 text-center text-terroir-dark/40">Aucune notification pour le moment.</p>
    @endforelse
</div>

<div class="mt-6">
    {{ $notifications->links() }}
</div>
@endsection
