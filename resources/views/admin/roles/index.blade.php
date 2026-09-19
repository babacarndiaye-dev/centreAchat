@extends('layouts.admin')

@section('title', 'Rôles')

@section('content')
<div class="uk-flex uk-flex-middle uk-flex-between">
    <p class="uk-text-small uk-text-muted">Définissez les rôles internes et les permissions accordées à chacun.</p>
    <a href="{{ route('admin.roles.create') }}" class="uk-button uk-button-primary">Nouveau rôle</a>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Permissions</th>
                <th>Utilisateurs</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
                <tr>
                    <td style="font-weight:600;">
                        {{ $role->name }}
                        @if($role->is_system)
                            <span class="uk-label" style="margin-left:8px; background:rgba(240,169,59,.15); color:#F0A93B;">système</span>
                        @endif
                    </td>
                    <td class="uk-text-muted">{{ $role->description ?: '—' }}</td>
                    <td>{{ $role->permissions_count }}</td>
                    <td>{{ $role->users_count }}</td>
                    <td class="uk-text-right">
                        <a href="{{ route('admin.roles.edit', $role) }}" style="font-weight:600; color:#1D8A4E;">Modifier</a>
                        @unless($role->is_system)
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Supprimer ce rôle ?')" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" style="margin-left:12px; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Supprimer</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucun rôle défini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
