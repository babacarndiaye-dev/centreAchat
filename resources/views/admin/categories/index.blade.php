@extends('layouts.admin')

@section('title', 'Catégories')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-sm text-terroir-dark/50">{{ $categories->total() }} catégorie(s)</p>
    <a href="{{ route('admin.categories.create') }}" class="btn-primary">
        <span class="material-symbols-outlined text-lg">add</span>
        Nouvelle catégorie
    </a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Nom</th>
                <th>Catégorie parente</th>
                <th>Position</th>
                <th>Statut</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $category->name }}</td>
                    <td class="text-terroir-dark/60">{{ $category->parent?->name ?? '—' }}</td>
                    <td>{{ $category->position }}</td>
                    <td>
                        @if($category->is_active)
                            <span class="admin-badge-success">Active</span>
                        @else
                            <span class="admin-badge-neutral">Inactive</span>
                        @endif
                    </td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="admin-link">Modifier</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette catégorie ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-terroir-dark/40">Aucune catégorie.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $categories->links() }}</div>
@endsection
