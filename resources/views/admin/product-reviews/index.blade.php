@extends('layouts.admin')

@section('title', 'Avis produits')

@section('content')
<p class="text-sm text-terroir-dark/50">{{ $reviews->total() }} avis produits (venant des fiches produits, distincts des avis clients généraux)</p>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Produit</th>
                <th>Client</th>
                <th>Note</th>
                <th>Commentaire</th>
                <th>Statut</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $review->product->name }}</td>
                    <td class="text-terroir-dark/70">{{ $review->user->name }}</td>
                    <td class="text-terroir-gold">{{ str_repeat('★', $review->rating) }}</td>
                    <td class="text-terroir-dark/60">{{ \Illuminate\Support\Str::limit($review->comment, 80) ?: '—' }}</td>
                    <td>
                        @if($review->is_approved)
                            <span class="admin-badge-success">Publié</span>
                        @else
                            <span class="admin-badge-neutral">Masqué</span>
                        @endif
                    </td>
                    <td class="pr-6 text-right">
                        <form action="{{ route('admin.avis-produits.toggle', $review) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="admin-link bg-transparent">{{ $review->is_approved ? 'Masquer' : 'Republier' }}</button>
                        </form>
                        <form action="{{ route('admin.avis-produits.destroy', $review) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cet avis ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-8 text-center text-terroir-dark/40">Aucun avis produit.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $reviews->links() }}</div>
@endsection
