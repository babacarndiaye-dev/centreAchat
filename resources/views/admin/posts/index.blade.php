@extends('layouts.admin')

@section('title', 'Actualités')

@section('content')
<div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap" style="gap:12px;">
    <p class="uk-text-small uk-text-muted">{{ $posts->total() }} article(s)</p>
    <a href="{{ route('admin.articles.create') }}" class="uk-button uk-button-primary">+ Nouvel article</a>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Type</th>
                <th>Statut</th>
                <th class="uk-text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
                <tr>
                    <td style="font-weight:600;">{{ $post->title }}</td>
                    <td class="uk-text-muted">{{ ucfirst($post->type) }}</td>
                    <td>
                        @if($post->is_published)
                            <span class="uk-label" style="background:rgba(29,138,78,.12); color:#1D8A4E;">Publié</span>
                        @else
                            <span class="uk-label" style="background:rgba(31,35,40,.08); color:rgba(31,35,40,.6);">Brouillon</span>
                        @endif
                    </td>
                    <td class="uk-text-right">
                        <a href="{{ route('admin.articles.edit', $post) }}" style="font-weight:600; color:#1D8A4E;">Modifier</a>
                        <form action="{{ route('admin.articles.destroy', $post) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cet article ?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="margin-left:12px; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucun article.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $posts->links() }}</div>
@endsection
