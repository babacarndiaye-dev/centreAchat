@extends('layouts.admin')

@section('title', 'Utilisateurs internes')

@section('content')
<div class="uk-flex uk-flex-middle uk-flex-between">
    <p class="uk-text-small uk-text-muted">Comptes du personnel ayant accès à l'administration.</p>
    <a href="{{ route('admin.utilisateurs.create') }}" class="uk-button uk-button-primary">Nouvel utilisateur</a>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td style="font-weight:600;">
                        {{ $user->name }}
                        @if($user->is_admin)
                            <span class="uk-label" style="margin-left:8px; background:rgba(240,169,59,.15); color:#F0A93B;">super admin</span>
                        @endif
                    </td>
                    <td class="uk-text-muted">{{ $user->email }}</td>
                    <td>{{ $user->role?->name ?? '—' }}</td>
                    <td>
                        @if($user->is_active)
                            <span style="font-size:.75rem; font-weight:600; color:#1D8A4E;">Actif</span>
                        @else
                            <span style="font-size:.75rem; font-weight:600; color:rgba(31,35,40,.4);">Inactif</span>
                        @endif
                    </td>
                    <td class="uk-text-right">
                        <a href="{{ route('admin.utilisateurs.edit', $user) }}" style="font-weight:600; color:#1D8A4E;">Modifier</a>
                        @unless($user->id === auth()->id())
                            <form action="{{ route('admin.utilisateurs.destroy', $user) }}" method="POST" onsubmit="return confirm('Retirer l\'accès staff de cet utilisateur ?')" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" style="margin-left:12px; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Retirer l'accès</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucun utilisateur interne.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
