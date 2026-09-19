@extends('layouts.admin')

@section('title', 'Pages')

@section('content')
<div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap" style="gap:12px;">
    <p class="uk-text-small uk-text-muted">{{ $pages->total() }} page(s)</p>
    <a href="{{ route('admin.pages.create') }}" class="uk-button uk-button-primary">+ Nouvelle page</a>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Slug</th>
                <th>Statut</th>
                <th class="uk-text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pages as $page)
                <tr>
                    <td style="font-weight:600;">{{ $page->title }}</td>
                    <td class="uk-text-muted">/{{ $page->slug }}</td>
                    <td>
                        @if($page->is_published)
                            <span class="uk-label" style="background:rgba(29,138,78,.12); color:#1D8A4E;">Publiée</span>
                        @else
                            <span class="uk-label" style="background:rgba(31,35,40,.08); color:rgba(31,35,40,.6);">Brouillon</span>
                        @endif
                    </td>
                    <td class="uk-text-right">
                        <a href="{{ route('admin.pages.edit', $page) }}" style="font-weight:600; color:#1D8A4E;">Modifier</a>
                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette page ?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="margin-left:12px; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucune page.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $pages->links() }}</div>
@endsection
