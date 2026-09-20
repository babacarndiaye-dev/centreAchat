@extends('layouts.admin')

@section('title', 'Devis')

@section('content')
<form method="GET" class="flex gap-2">
    <select name="status" class="input max-w-[260px]" onchange="this.form.submit()">
        <option value="">Tous les statuts</option>
        @foreach(\App\Models\Quote::STATUSES as $value => $label)
            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</form>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">N°</th>
                <th>Client</th>
                <th>Date</th>
                <th>Statut</th>
                <th class="text-right">Total</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quotes as $quote)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $quote->quote_number }}</td>
                    <td>{{ $quote->user->name }}</td>
                    <td class="text-terroir-dark/60">{{ $quote->created_at->format('d/m/Y') }}</td>
                    <td><span class="{{ $quote->statusBadgeClass() }}">{{ \App\Models\Quote::STATUSES[$quote->status] }}</span></td>
                    <td class="text-right font-semibold">{{ $quote->total > 0 ? number_format($quote->total, 0, ',', ' ').' FCFA' : '—' }}</td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.devis.show', $quote) }}" class="admin-link">Traiter</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-8 text-center text-terroir-dark/40">Aucun devis.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $quotes->links() }}</div>
@endsection
