@extends('layouts.admin')

@section('title', 'Écritures comptables')

@section('content')
<div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap" style="gap:12px;">
    <form method="GET" class="uk-flex uk-flex-wrap" style="gap:8px;">
        <select name="journal" class="uk-select" style="max-width:220px;" onchange="this.form.submit()">
            <option value="">Tous les journaux</option>
            @foreach($journals as $journal)
                <option value="{{ $journal->id }}" @selected((string) request('journal') === (string) $journal->id)>{{ $journal->code }} — {{ $journal->name }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}" class="uk-input">
        <input type="date" name="to" value="{{ request('to') }}" class="uk-input">
        <button type="submit" class="uk-button uk-button-default">Filtrer</button>
    </form>
    <a href="{{ route('admin.comptabilite.ecritures.create') }}" class="uk-button uk-button-primary">+ Nouvelle écriture</a>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-small uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Date</th>
                <th>Journal</th>
                <th>Libellé</th>
                <th>Référence</th>
                <th class="uk-text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
                <tr>
                    <td class="uk-text-muted">{{ $entry->entry_date->format('d/m/Y') }}</td>
                    <td><span class="uk-label" style="background:#F7F8F5; color:#1F2328;">{{ $entry->journal->code }}</span></td>
                    <td><a href="{{ route('admin.comptabilite.ecritures.show', $entry) }}" style="font-weight:600; color:#1D8A4E;">{{ $entry->description }}</a></td>
                    <td class="uk-text-muted">{{ $entry->reference }}</td>
                    <td class="uk-text-right" style="font-weight:600;">{{ number_format($entry->total_debit, 0, ',', ' ') }} FCFA</td>
                </tr>
            @empty
                <tr><td colspan="5" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucune écriture.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $entries->links() }}</div>
@endsection
