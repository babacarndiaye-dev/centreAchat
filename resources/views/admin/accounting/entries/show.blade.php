@extends('layouts.admin')

@section('title', $entry->description)

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <h2 class="font-display text-2xl font-semibold">{{ $entry->description }}</h2>
        <p class="text-sm text-terroir-dark/50">{{ $entry->journal->code }} — {{ $entry->entry_date->format('d/m/Y') }} @if($entry->reference) — Réf. {{ $entry->reference }} @endif</p>
    </div>
    @if(!$entry->source_type)
        <form action="{{ route('admin.comptabilite.ecritures.destroy', $entry) }}" method="POST" onsubmit="return confirm('Supprimer cette écriture ?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-outline text-terroir-terracotta">Supprimer</button>
        </form>
    @endif
</div>

<div class="admin-card mt-6">
    <dl class="grid gap-4 sm:grid-cols-2">
        <div>
            <dt class="text-sm text-terroir-dark/50">Journal</dt>
            <dd class="font-semibold">{{ $entry->journal->code }} — {{ $entry->journal->name }}</dd>
        </div>
        <div>
            <dt class="text-sm text-terroir-dark/50">Date</dt>
            <dd>{{ $entry->entry_date->format('d/m/Y') }}</dd>
        </div>
        <div>
            <dt class="text-sm text-terroir-dark/50">Référence</dt>
            <dd>{{ $entry->reference ?: '—' }}</dd>
        </div>
        <div>
            <dt class="text-sm text-terroir-dark/50">Libellé</dt>
            <dd>{{ $entry->description }}</dd>
        </div>
    </dl>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Compte</th>
                <th>Libellé</th>
                <th class="text-right">Débit</th>
                <th class="pr-6 text-right">Crédit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entry->lines as $line)
                <tr>
                    <td class="pl-6 font-mono">{{ $line->account->code }} — {{ $line->account->name }}</td>
                    <td class="text-terroir-dark/60">{{ $line->label }}</td>
                    <td class="text-right">{{ $line->debit > 0 ? number_format($line->debit, 0, ',', ' ') : '' }}</td>
                    <td class="pr-6 text-right">{{ $line->credit > 0 ? number_format($line->credit, 0, ',', ' ') : '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="border-t-2 border-terroir-dark/20 font-bold text-terroir-green">
                <td class="pl-6" colspan="2">Total</td>
                <td class="text-right">{{ number_format($entry->totalDebit(), 0, ',', ' ') }}</td>
                <td class="pr-6 text-right">{{ number_format($entry->totalCredit(), 0, ',', ' ') }}</td>
            </tr>
        </tfoot>
    </table>
    @if($entry->source_type)
        <p class="p-5 text-sm text-terroir-dark/50">Écriture générée automatiquement.</p>
    @endif
</div>
@endsection
