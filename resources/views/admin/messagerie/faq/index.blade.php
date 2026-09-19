@extends('layouts.admin')

@section('title', 'Base de connaissances (FAQ)')

@section('content')
@php($aiEnabled = config('services.openai.chat_enabled') && filled(config('services.openai.api_key')))
<div class="uk-flex uk-flex-between uk-flex-wrap" style="gap:12px;">
    <div>
        <a href="{{ route('admin.messagerie.index') }}" class="uk-text-small uk-text-muted">&larr; Retour à la messagerie</a>
        <p class="uk-margin-small-top uk-text-small uk-text-muted">Ces questions/réponses alimentent les réponses automatiques du chatbot par correspondance de mots-clés. Si aucune ne correspond, une IA de secours prend le relais en s'appuyant uniquement sur ce contenu — jamais sur ses propres connaissances.</p>
        <p class="uk-margin-small-top uk-text-small">
            IA de secours :
            @if($aiEnabled)
                <span style="font-weight:600; color:#1D8A4E;">🧠 Active ({{ config('services.openai.model') }})</span>
            @else
                <span style="font-weight:600; color:rgba(31,35,40,.4);">Inactive — aucune clé API configurée (variable OPENAI_API_KEY)</span>
            @endif
        </p>
    </div>
    <a href="{{ route('admin.messagerie.faq.create') }}" class="uk-button uk-button-primary">Nouvelle question</a>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Question</th>
                <th>Catégorie</th>
                <th>Utilisée</th>
                <th>Statut</th>
                <th class="uk-text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
                <tr>
                    <td style="font-weight:600;">{{ $entry->question }}</td>
                    <td class="uk-text-muted">{{ $entry->category ?: '—' }}</td>
                    <td class="uk-text-muted">{{ $entry->hit_count }} fois</td>
                    <td>
                        @if($entry->is_active)
                            <span class="uk-label" style="background:rgba(29,138,78,.12); color:#1D8A4E;">Active</span>
                        @else
                            <span class="uk-label" style="background:rgba(31,35,40,.08); color:rgba(31,35,40,.6);">Inactive</span>
                        @endif
                    </td>
                    <td class="uk-text-right">
                        <a href="{{ route('admin.messagerie.faq.edit', $entry) }}" style="font-weight:600; color:#1D8A4E;">Modifier</a>
                        <form action="{{ route('admin.messagerie.faq.destroy', $entry) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette question ?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="margin-left:12px; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucune question dans la base de connaissances.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
