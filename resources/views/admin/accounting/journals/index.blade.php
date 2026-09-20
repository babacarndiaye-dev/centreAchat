@extends('layouts.admin')

@section('title', 'Journaux comptables')

@section('content')
<div class="admin-card max-w-2xl">
    <h2 class="font-display text-lg font-semibold">Nouveau journal</h2>
    <form action="{{ route('admin.comptabilite.journaux.store') }}" method="POST" class="mt-3 flex flex-wrap gap-3">
        @csrf
        <input type="text" name="code" placeholder="Code" required class="input w-28">
        <input type="text" name="name" placeholder="Intitulé" required class="input min-w-[180px] flex-1">
        <select name="type" required class="input w-40">
            @foreach(\App\Models\Journal::TYPES as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary w-full justify-center">Ajouter</button>
    </form>

    <div class="mt-4 flex flex-col gap-2">
        @foreach($journals as $journal)
            <div class="flex items-center justify-between rounded-lg bg-terroir-cream px-4 py-3">
                <div>
                    <span class="font-mono font-semibold">{{ $journal->code }}</span>
                    <span class="ml-2">{{ $journal->name }}</span>
                    <span class="ml-2 text-sm text-terroir-dark/50">({{ \App\Models\Journal::TYPES[$journal->type] }})</span>
                </div>
                <form action="{{ route('admin.comptabilite.journaux.destroy', $journal) }}" method="POST" onsubmit="return confirm('Supprimer ce journal ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="admin-link-danger bg-transparent">Supprimer</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
