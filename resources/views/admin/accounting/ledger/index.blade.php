@extends('layouts.admin')

@section('title', 'Grand livre')

@section('content')
<form method="GET" class="uk-flex" style="gap:8px;">
    <select name="compte" class="uk-select" style="max-width:28rem;" onchange="this.form.submit()">
        <option value="">Choisir un compte...</option>
        @foreach($accounts as $acc)
            <option value="{{ $acc->id }}" @selected($account?->id === $acc->id)>{{ $acc->code }} — {{ $acc->name }}</option>
        @endforeach
    </select>
</form>

@if($account)
    <div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
        <div style="background:#F7F8F5; padding:12px 20px; font-family:'Fraunces',serif; font-weight:600; font-size:0.875rem; color:#1D8A4E;">{{ $account->code }} — {{ $account->name }}</div>
        <table class="uk-table uk-table-divider uk-table-small uk-table-middle" style="margin:0;">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Libellé</th>
                    <th class="uk-text-right">Débit</th>
                    <th class="uk-text-right">Crédit</th>
                    <th class="uk-text-right">Solde</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lines as $line)
                    <tr>
                        <td class="uk-text-muted">{{ $line->entry->entry_date->format('d/m/Y') }}</td>
                        <td><a href="{{ route('admin.comptabilite.ecritures.show', $line->entry) }}" style="color:#1D8A4E;">{{ $line->entry->description }}</a> {{ $line->label ? '— '.$line->label : '' }}</td>
                        <td class="uk-text-right">{{ $line->debit > 0 ? number_format($line->debit, 0, ',', ' ') : '' }}</td>
                        <td class="uk-text-right">{{ $line->credit > 0 ? number_format($line->credit, 0, ',', ' ') : '' }}</td>
                        <td class="uk-text-right" style="font-weight:600; {{ $line->running_balance < 0 ? 'color:#E8604F;' : '' }}">{{ number_format($line->running_balance, 0, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucun mouvement sur ce compte.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@else
    <p class="uk-margin-top uk-text-muted">Sélectionnez un compte pour consulter son grand livre.</p>
@endif
@endsection
