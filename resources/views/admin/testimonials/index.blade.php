@extends('layouts.admin')

@section('title', 'Avis clients')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-terroir-dark/50">{{ $testimonials->total() }} avis</p>
    <a href="{{ route('admin.avis.create') }}" class="btn-primary">
        <span class="material-symbols-outlined text-lg">add</span>
        Nouvel avis
    </a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Auteur</th>
                <th>Note</th>
                <th>Statut</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($testimonials as $testimonial)
                <tr>
                    <td class="pl-6">
                        <p class="font-semibold text-terroir-dark">{{ $testimonial->author_name }}</p>
                        <p class="text-xs text-terroir-dark/50">{{ $testimonial->author_role }}</p>
                    </td>
                    <td class="text-terroir-gold">
                        <span class="inline-flex gap-0.5">
                            @for($s = 1; $s <= 5; $s++)
                                <span class="material-symbols-outlined text-base{{ $s <= $testimonial->rating ? ' is-filled' : '' }}">star</span>
                            @endfor
                        </span>
                    </td>
                    <td>
                        @if($testimonial->is_published)
                            <span class="admin-badge-success">Publié</span>
                        @else
                            <span class="admin-badge-neutral">Masqué</span>
                        @endif
                    </td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.avis.edit', $testimonial) }}" class="admin-link">Modifier</a>
                        <form action="{{ route('admin.avis.destroy', $testimonial) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cet avis ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-8 text-center text-terroir-dark/40">Aucun avis.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $testimonials->links() }}</div>
@endsection
