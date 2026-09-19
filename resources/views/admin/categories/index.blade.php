@extends('layouts.admin')

@section('title', 'Catégories')

@section('content')
<div class="uk-flex uk-flex-between uk-flex-middle">
    <p class="uk-text-small uk-text-muted">{{ $categories->total() }} catégorie(s)</p>
    <a href="{{ route('admin.categories.create') }}" class="uk-button uk-button-primary">+ Nouvelle catégorie</a>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Catégorie parente</th>
                <th>Position</th>
                <th>Statut</th>
                <th class="uk-text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td style="font-weight:600;">{{ $category->name }}</td>
                    <td class="uk-text-muted">{{ $category->parent?->name ?? '—' }}</td>
                    <td>{{ $category->position }}</td>
                    <td>
                        @if($category->is_active)
                            <span class="uk-label" style="background:rgba(29,138,78,.12); color:#1D8A4E;">Active</span>
                        @else
                            <span class="uk-label" style="background:rgba(31,35,40,.08); color:rgba(31,35,40,.6);">Inactive</span>
                        @endif
                    </td>
                    <td class="uk-text-right">
                        <a href="{{ route('admin.categories.edit', $category) }}" style="font-weight:600; color:#1D8A4E;">Modifier</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette catégorie ?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="margin-left:12px; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucune catégorie.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $categories->links() }}</div>
@endsection
