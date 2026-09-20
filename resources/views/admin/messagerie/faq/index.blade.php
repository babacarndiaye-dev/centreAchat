@extends('layouts.admin')

@section('title', 'Base de connaissances (FAQ)')

@section('content')
@php($aiEnabled = config('services.openai.chat_enabled') && filled(config('services.openai.api_key')))
<div class="flex flex-wrap items-start justify-between gap-3">
    <div>
        <a href="{{ route('admin.messagerie.index') }}" class="text-sm text-terroir-dark/50">&larr; Retour à la messagerie</a>
        <p class="mt-1.5 text-sm text-terroir-dark/50">Ces questions/réponses alimentent les réponses automatiques du chatbot par correspondance de mots-clés. Si aucune ne correspond, une IA de secours prend le relais en s'appuyant uniquement sur ce contenu — jamais sur ses propres connaissances.</p>
        <p class="mt-1.5 text-sm">
            IA de secours :
            @if($aiEnabled)
                <span class="font-semibold text-terroir-green">🧠 Active ({{ config('services.openai.model') }})</span>
            @else
                <span class="font-semibold text-terroir-dark/40">Inactive — aucune clé API configurée (variable OPENAI_API_KEY)</span>
            @endif
        </p>
    </div>
    <a href="{{ route('admin.messagerie.faq.create') }}" class="btn-primary">Nouvelle question</a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Question</th>
                <th>Catégorie</th>
                <th>Utilisée</th>
                <th>Statut</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $entry->question }}</td>
                    <td class="text-terroir-dark/60">{{ $entry->category ?: '—' }}</td>
                    <td class="text-terroir-dark/60">{{ $entry->hit_count }} fois</td>
                    <td>
                        @if($entry->is_active)
                            <span class="admin-badge-success">Active</span>
                        @else
                            <span class="admin-badge-neutral">Inactive</span>
                        @endif
                    </td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.messagerie.faq.edit', $entry) }}" class="admin-link">Modifier</a>
                        <form action="{{ route('admin.messagerie.faq.destroy', $entry) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette question ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-terroir-dark/40">Aucune question dans la base de connaissances.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
