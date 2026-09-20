@extends('layouts.admin')

@section('title', 'Écritures comptables')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="journal" class="input max-w-[220px]" onchange="this.form.submit()">
            <option value="">Tous les journaux</option>
            @foreach($journals as $journal)
                <option value="{{ $journal->id }}" @selected((string) request('journal') === (string) $journal->id)>{{ $journal->code }} — {{ $journal->name }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}" class="input">
        <input type="date" name="to" value="{{ request('to') }}" class="input">
        <button type="submit" class="btn-outline">Filtrer</button>
    </form>
    <a href="{{ route('admin.comptabilite.ecritures.create') }}" class="btn-primary">
        <span class="material-symbols-outlined text-lg">add</span>
        Nouvelle écriture
    </a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Date</th>
                <th>Journal</th>
                <th>Libellé</th>
                <th>Référence</th>
                <th class="pr-6 text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
                <tr>
                    <td class="pl-6 text-terroir-dark/60">{{ $entry->entry_date->format('d/m/Y') }}</td>
                    <td><span class="admin-badge-neutral">{{ $entry->journal->code }}</span></td>
                    <td><a href="{{ route('admin.comptabilite.ecritures.show', $entry) }}" class="admin-link">{{ $entry->description }}</a></td>
                    <td class="text-terroir-dark/60">{{ $entry->reference }}</td>
                    <td class="pr-6 text-right font-semibold">{{ number_format($entry->total_debit, 0, ',', ' ') }} FCFA</td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-terroir-dark/40">Aucune écriture.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $entries->links() }}</div>
@endsection
