@extends('layouts.admin')

@section('title', 'Rôles')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-sm text-terroir-dark/50">Définissez les rôles internes et les permissions accordées à chacun.</p>
    <a href="{{ route('admin.roles.create') }}" class="btn-primary">Nouveau rôle</a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Nom</th>
                <th>Description</th>
                <th>Permissions</th>
                <th>Utilisateurs</th>
                <th class="pr-6"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">
                        {{ $role->name }}
                        @if($role->is_system)
                            <span class="admin-badge-warning ml-2">système</span>
                        @endif
                    </td>
                    <td class="text-terroir-dark/60">{{ $role->description ?: '—' }}</td>
                    <td>{{ $role->permissions_count }}</td>
                    <td>{{ $role->users_count }}</td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="admin-link">Modifier</a>
                        @unless($role->is_system)
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Supprimer ce rôle ?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-terroir-dark/40">Aucun rôle défini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
