@extends('layouts.admin')

@section('title', 'Pages')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-terroir-dark/50">{{ $pages->total() }} page(s)</p>
    <a href="{{ route('admin.pages.create') }}" class="btn-primary">
        <span class="material-symbols-outlined text-lg">add</span>
        Nouvelle page
    </a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Titre</th>
                <th>Slug</th>
                <th>Statut</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pages as $page)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $page->title }}</td>
                    <td class="text-terroir-dark/60">/{{ $page->slug }}</td>
                    <td>
                        @if($page->is_published)
                            <span class="admin-badge-success">Publiée</span>
                        @else
                            <span class="admin-badge-neutral">Brouillon</span>
                        @endif
                    </td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.pages.edit', $page) }}" class="admin-link">Modifier</a>
                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette page ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-8 text-center text-terroir-dark/40">Aucune page.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $pages->links() }}</div>
@endsection
