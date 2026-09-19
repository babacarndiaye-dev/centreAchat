@extends('layouts.admin')

@section('title', 'Journaux comptables')

@section('content')
<div class="uk-card uk-card-default" style="max-width:40rem; padding:32px;">
    <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Nouveau journal</h2>
    <form action="{{ route('admin.comptabilite.journaux.store') }}" method="POST" class="uk-grid-small uk-child-width-1-4@s uk-margin-top" uk-grid>
        @csrf
        <div><input type="text" name="code" placeholder="Code" required class="uk-input"></div>
        <div class="uk-width-1-2@s"><input type="text" name="name" placeholder="Intitulé" required class="uk-input"></div>
        <div>
            <select name="type" required class="uk-select">
                @foreach(\App\Models\Journal::TYPES as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="uk-width-1-1"><button type="submit" class="uk-button uk-button-primary uk-width-1-1">Ajouter</button></div>
    </form>

    <div class="uk-margin-top" style="display:flex; flex-direction:column; gap:8px;">
        @foreach($journals as $journal)
            <div class="uk-flex uk-flex-between uk-flex-middle" style="border-radius:8px; background:#F7F8F5; padding:12px 16px;">
                <div>
                    <span style="font-family:monospace; font-weight:600;">{{ $journal->code }}</span>
                    <span style="margin-left:8px;">{{ $journal->name }}</span>
                    <span class="uk-text-small uk-text-muted" style="margin-left:8px;">({{ \App\Models\Journal::TYPES[$journal->type] }})</span>
                </div>
                <form action="{{ route('admin.comptabilite.journaux.destroy', $journal) }}" method="POST" onsubmit="return confirm('Supprimer ce journal ?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Supprimer</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
