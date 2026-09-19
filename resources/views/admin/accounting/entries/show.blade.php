@extends('layouts.admin')

@section('title', $entry->description)

@section('content')
<div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap" style="gap:16px;">
    <div>
        <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.5rem;">{{ $entry->description }}</h2>
        <p class="uk-text-small uk-text-muted">{{ $entry->journal->code }} — {{ $entry->entry_date->format('d/m/Y') }} @if($entry->reference) — Réf. {{ $entry->reference }} @endif</p>
    </div>
    @if(!$entry->source_type)
        <form action="{{ route('admin.comptabilite.ecritures.destroy', $entry) }}" method="POST" onsubmit="return confirm('Supprimer cette écriture ?')">
            @csrf @method('DELETE')
            <button type="submit" class="uk-button uk-button-default" style="color:#E8604F;">Supprimer</button>
        </form>
    @endif
</div>

<div class="uk-card uk-card-default uk-margin-top" style="padding:24px;">
    <dl class="uk-grid-small uk-child-width-1-2@s" uk-grid>
        <div>
            <dt class="uk-text-small uk-text-muted">Journal</dt>
            <dd style="font-weight:600;">{{ $entry->journal->code }} — {{ $entry->journal->name }}</dd>
        </div>
        <div>
            <dt class="uk-text-small uk-text-muted">Date</dt>
            <dd>{{ $entry->entry_date->format('d/m/Y') }}</dd>
        </div>
        <div>
            <dt class="uk-text-small uk-text-muted">Référence</dt>
            <dd>{{ $entry->reference ?: '—' }}</dd>
        </div>
        <div>
            <dt class="uk-text-small uk-text-muted">Libellé</dt>
            <dd>{{ $entry->description }}</dd>
        </div>
    </dl>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-small uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Compte</th>
                <th>Libellé</th>
                <th class="uk-text-right">Débit</th>
                <th class="uk-text-right">Crédit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entry->lines as $line)
                <tr>
                    <td style="font-family:monospace;">{{ $line->account->code }} — {{ $line->account->name }}</td>
                    <td class="uk-text-muted">{{ $line->label }}</td>
                    <td class="uk-text-right">{{ $line->debit > 0 ? number_format($line->debit, 0, ',', ' ') : '' }}</td>
                    <td class="uk-text-right">{{ $line->credit > 0 ? number_format($line->credit, 0, ',', ' ') : '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="border-top:2px solid rgba(31,35,40,.2); font-weight:700; color:#1D8A4E;">
                <td colspan="2">Total</td>
                <td class="uk-text-right">{{ number_format($entry->totalDebit(), 0, ',', ' ') }}</td>
                <td class="uk-text-right">{{ number_format($entry->totalCredit(), 0, ',', ' ') }}</td>
            </tr>
        </tfoot>
    </table>
    @if($entry->source_type)
        <p class="uk-text-small uk-text-muted" style="padding:16px 20px;">Écriture générée automatiquement.</p>
    @endif
</div>
@endsection
