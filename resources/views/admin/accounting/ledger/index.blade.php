@extends('layouts.admin')

@section('title', 'Grand livre')

@section('content')
<form method="GET" class="flex gap-2">
    <select name="compte" class="input max-w-md" onchange="this.form.submit()">
        <option value="">Choisir un compte...</option>
        @foreach($accounts as $acc)
            <option value="{{ $acc->id }}" @selected($account?->id === $acc->id)>{{ $acc->code }} — {{ $acc->name }}</option>
        @endforeach
    </select>
</form>

@if($account)
    <div class="admin-card mt-6 overflow-x-auto p-0">
        <div class="bg-terroir-cream px-5 py-3 font-display text-sm font-semibold text-terroir-green">{{ $account->code }} — {{ $account->name }}</div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="pl-6">Date</th>
                    <th>Libellé</th>
                    <th class="text-right">Débit</th>
                    <th class="text-right">Crédit</th>
                    <th class="pr-6 text-right">Solde</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lines as $line)
                    <tr>
                        <td class="pl-6 text-terroir-dark/60">{{ $line->entry->entry_date->format('d/m/Y') }}</td>
                        <td><a href="{{ route('admin.comptabilite.ecritures.show', $line->entry) }}" class="admin-link">{{ $line->entry->description }}</a> {{ $line->label ? '— '.$line->label : '' }}</td>
                        <td class="text-right">{{ $line->debit > 0 ? number_format($line->debit, 0, ',', ' ') : '' }}</td>
                        <td class="text-right">{{ $line->credit > 0 ? number_format($line->credit, 0, ',', ' ') : '' }}</td>
                        <td class="pr-6 text-right font-semibold {{ $line->running_balance < 0 ? 'text-terroir-terracotta' : '' }}">{{ number_format($line->running_balance, 0, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-8 text-center text-terroir-dark/40">Aucun mouvement sur ce compte.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@else
    <p class="mt-6 text-terroir-dark/50">Sélectionnez un compte pour consulter son grand livre.</p>
@endif
@endsection
