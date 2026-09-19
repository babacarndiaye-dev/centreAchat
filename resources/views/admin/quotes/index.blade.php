@extends('layouts.admin')

@section('title', 'Devis')

@section('content')
<form method="GET" class="uk-flex" style="gap:8px;">
    <select name="status" class="uk-select" style="max-width:260px;" onchange="this.form.submit()">
        <option value="">Tous les statuts</option>
        @foreach(\App\Models\Quote::STATUSES as $value => $label)
            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</form>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>N°</th>
                <th>Client</th>
                <th>Date</th>
                <th>Statut</th>
                <th class="uk-text-right">Total</th>
                <th class="uk-text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quotes as $quote)
                <tr>
                    <td style="font-weight:600;">{{ $quote->quote_number }}</td>
                    <td>{{ $quote->user->name }}</td>
                    <td class="uk-text-muted">{{ $quote->created_at->format('d/m/Y') }}</td>
                    <td><span class="uk-label" style="background:#F7F8F5; color:#1D8A4E;">{{ \App\Models\Quote::STATUSES[$quote->status] }}</span></td>
                    <td class="uk-text-right" style="font-weight:600;">{{ $quote->total > 0 ? number_format($quote->total, 0, ',', ' ').' FCFA' : '—' }}</td>
                    <td class="uk-text-right">
                        <a href="{{ route('admin.devis.show', $quote) }}" style="font-weight:600; color:#1D8A4E;">Traiter</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucun devis.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $quotes->links() }}</div>
@endsection
