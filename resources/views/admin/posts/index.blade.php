@extends('layouts.admin')

@section('title', 'Actualités')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-terroir-dark/50">{{ $posts->total() }} article(s)</p>
    <a href="{{ route('admin.articles.create') }}" class="btn-primary">
        <span class="material-symbols-outlined text-lg">add</span>
        Nouvel article
    </a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Titre</th>
                <th>Type</th>
                <th>Statut</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $post->title }}</td>
                    <td class="text-terroir-dark/60">{{ ucfirst($post->type) }}</td>
                    <td>
                        @if($post->is_published)
                            <span class="admin-badge-success">Publié</span>
                        @else
                            <span class="admin-badge-neutral">Brouillon</span>
                        @endif
                    </td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.articles.edit', $post) }}" class="admin-link">Modifier</a>
                        <form action="{{ route('admin.articles.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cet article ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-8 text-center text-terroir-dark/40">Aucun article.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $posts->links() }}</div>
@endsection
