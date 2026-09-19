@extends('layouts.admin')

@section('title', 'Balance générale')

@section('content')
<div class="uk-card uk-card-default" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-small uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Compte</th>
                <th class="uk-text-right">Débit</th>
                <th class="uk-text-right">Crédit</th>
                <th class="uk-text-right">Solde</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $account)
                <tr>
                    <td><a href="{{ route('admin.comptabilite.grand-livre.index', ['compte' => $account->id]) }}" style="font-family:monospace; color:#1D8A4E;">{{ $account->code }}</a> — {{ $account->name }}</td>
                    <td class="uk-text-right">{{ number_format($account->debit_total, 0, ',', ' ') }}</td>
                    <td class="uk-text-right">{{ number_format($account->credit_total, 0, ',', ' ') }}</td>
                    <td class="uk-text-right" style="font-weight:600; {{ $account->solde < 0 ? 'color:#E8604F;' : '' }}">{{ number_format($account->solde, 0, ',', ' ') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucune écriture enregistrée.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="border-top:2px solid rgba(31,35,40,.2); font-weight:700; color:#1D8A4E;">
                <td>Total</td>
                <td class="uk-text-right">{{ number_format($totalDebit, 0, ',', ' ') }}</td>
                <td class="uk-text-right">{{ number_format($totalCredit, 0, ',', ' ') }}</td>
                <td class="uk-text-right">{{ round($totalDebit, 2) === round($totalCredit, 2) ? 'Équilibrée ✓' : 'Écart' }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
