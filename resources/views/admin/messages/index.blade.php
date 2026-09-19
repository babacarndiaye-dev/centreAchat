@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
<div class="admin-card overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Nom</th>
                <th>Sujet</th>
                <th>Date</th>
                <th>Statut</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($messages as $message)
                <tr class="{{ $message->is_read ? '' : 'font-semibold' }}">
                    <td class="pl-6">
                        {{ $message->name }}
                        <span class="block text-xs font-normal text-terroir-dark/50">{{ $message->email }}</span>
                    </td>
                    <td>{{ $message->subject ?? '—' }}</td>
                    <td class="text-terroir-dark/50">{{ $message->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="flex flex-wrap gap-1.5">
                            @if($message->is_read)
                                <span class="admin-badge-neutral">Lu</span>
                            @else
                                <span class="admin-badge-danger">Non lu</span>
                            @endif
                            @if($message->reply)
                                <span class="admin-badge-success">Répondu</span>
                            @endif
                        </div>
                    </td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.messages.show', $message) }}" class="admin-link">Lire</a>
                        <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce message ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-terroir-dark/40">Aucun message.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $messages->links() }}</div>
@endsection
