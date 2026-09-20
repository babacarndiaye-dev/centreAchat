@extends('layouts.admin')

@section('title', 'Producteurs')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-sm text-terroir-dark/50">{{ $producers->total() }} producteur(s)</p>
    <a href="{{ route('admin.producteurs.create') }}" class="btn-primary">
        <span class="material-symbols-outlined text-lg">add</span>
        Nouveau producteur
    </a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Nom</th>
                <th>Région</th>
                <th>Vedette</th>
                <th>Statut</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($producers as $producer)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $producer->name }}</td>
                    <td class="text-terroir-dark/60">{{ $producer->region ?? '—' }}</td>
                    <td>{{ $producer->is_featured ? 'Oui' : 'Non' }}</td>
                    <td>
                        @if($producer->is_active)
                            <span class="admin-badge-success">Actif</span>
                        @else
                            <span class="admin-badge-neutral">Inactif</span>
                        @endif
                    </td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.producteurs.edit', $producer) }}" class="admin-link">Modifier</a>
                        <form action="{{ route('admin.producteurs.destroy', $producer) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce producteur ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-terroir-dark/40">Aucun producteur.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $producers->links() }}</div>
@endsection
