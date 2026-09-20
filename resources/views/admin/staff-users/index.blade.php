@extends('layouts.admin')

@section('title', 'Utilisateurs internes')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-sm text-terroir-dark/50">Comptes du personnel ayant accès à l'administration.</p>
    <a href="{{ route('admin.utilisateurs.create') }}" class="btn-primary">Nouvel utilisateur</a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th class="pr-6"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">
                        {{ $user->name }}
                        @if($user->is_admin)
                            <span class="admin-badge-warning ml-2">super admin</span>
                        @endif
                    </td>
                    <td class="text-terroir-dark/60">{{ $user->email }}</td>
                    <td>{{ $user->role?->name ?? '—' }}</td>
                    <td>
                        @if($user->is_active)
                            <span class="text-xs font-semibold text-terroir-green">Actif</span>
                        @else
                            <span class="text-xs font-semibold text-terroir-dark/40">Inactif</span>
                        @endif
                    </td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.utilisateurs.edit', $user) }}" class="admin-link">Modifier</a>
                        @unless($user->id === auth()->id())
                            <form action="{{ route('admin.utilisateurs.destroy', $user) }}" method="POST" onsubmit="return confirm('Retirer l\'accès staff de cet utilisateur ?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-link-danger ml-3 bg-transparent">Retirer l'accès</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-terroir-dark/40">Aucun utilisateur interne.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
