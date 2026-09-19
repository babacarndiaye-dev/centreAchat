@extends('layouts.admin')

@section('title', 'Producteurs')

@section('content')
<div class="uk-flex uk-flex-between uk-flex-middle">
    <p class="uk-text-small uk-text-muted">{{ $producers->total() }} producteur(s)</p>
    <a href="{{ route('admin.producteurs.create') }}" class="uk-button uk-button-primary">+ Nouveau producteur</a>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Région</th>
                <th>Vedette</th>
                <th>Statut</th>
                <th class="uk-text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($producers as $producer)
                <tr>
                    <td style="font-weight:600;">{{ $producer->name }}</td>
                    <td class="uk-text-muted">{{ $producer->region ?? '—' }}</td>
                    <td>{{ $producer->is_featured ? 'Oui' : 'Non' }}</td>
                    <td>
                        @if($producer->is_active)
                            <span class="uk-label" style="background:rgba(29,138,78,.12); color:#1D8A4E;">Actif</span>
                        @else
                            <span class="uk-label" style="background:rgba(31,35,40,.08); color:rgba(31,35,40,.6);">Inactif</span>
                        @endif
                    </td>
                    <td class="uk-text-right">
                        <a href="{{ route('admin.producteurs.edit', $producer) }}" style="font-weight:600; color:#1D8A4E;">Modifier</a>
                        <form action="{{ route('admin.producteurs.destroy', $producer) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce producteur ?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="margin-left:12px; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucun producteur.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $producers->links() }}</div>
@endsection
