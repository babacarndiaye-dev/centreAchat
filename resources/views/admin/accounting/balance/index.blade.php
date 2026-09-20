@extends('layouts.admin')

@section('title', 'Balance générale')

@section('content')
<div class="admin-card overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Compte</th>
                <th class="text-right">Débit</th>
                <th class="text-right">Crédit</th>
                <th class="pr-6 text-right">Solde</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $account)
                <tr>
                    <td class="pl-6"><a href="{{ route('admin.comptabilite.grand-livre.index', ['compte' => $account->id]) }}" class="font-mono text-terroir-green">{{ $account->code }}</a> — {{ $account->name }}</td>
                    <td class="text-right">{{ number_format($account->debit_total, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($account->credit_total, 0, ',', ' ') }}</td>
                    <td class="pr-6 text-right font-semibold {{ $account->solde < 0 ? 'text-terroir-terracotta' : '' }}">{{ number_format($account->solde, 0, ',', ' ') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-8 text-center text-terroir-dark/40">Aucune écriture enregistrée.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="border-t-2 border-terroir-dark/20 font-bold text-terroir-green">
                <td class="pl-6">Total</td>
                <td class="text-right">{{ number_format($totalDebit, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($totalCredit, 0, ',', ' ') }}</td>
                <td class="pr-6 text-right">{{ round($totalDebit, 2) === round($totalCredit, 2) ? 'Équilibrée ✓' : 'Écart' }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
