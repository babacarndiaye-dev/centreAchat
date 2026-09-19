@extends('layouts.admin')

@section('title', "Demandes d'achat")

@section('content')
@php
    $prStatusColors = [
        'brouillon' => ['bg' => 'rgba(240,169,59,.15)', 'color' => '#F0A93B'],
        'en_attente_validation' => ['bg' => 'rgba(240,169,59,.15)', 'color' => '#F0A93B'],
        'validee' => ['bg' => 'rgba(29,138,78,.12)', 'color' => '#1D8A4E'],
        'rejetee' => ['bg' => 'rgba(232,96,79,.12)', 'color' => '#E8604F'],
        'convertie' => ['bg' => 'rgba(29,138,78,.12)', 'color' => '#1D8A4E'],
        'annulee' => ['bg' => 'rgba(232,96,79,.12)', 'color' => '#E8604F'],
    ];
@endphp
<div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap" style="gap:12px;">
    <form method="GET" class="uk-flex" style="gap:8px;">
        <select name="status" class="uk-select" style="max-width:240px;" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach(\App\Models\PurchaseRequest::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('admin.demandes-achat.create') }}" class="uk-button uk-button-primary">+ Nouvelle demande d'achat</a>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Référence</th>
                <th>Demandeur</th>
                <th>Date</th>
                <th>Statut</th>
                <th class="uk-text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseRequests as $pr)
                <tr>
                    <td style="font-weight:600;">{{ $pr->reference }}</td>
                    <td class="uk-text-muted">{{ $pr->requester?->name ?? '—' }}</td>
                    <td class="uk-text-muted">{{ $pr->created_at->format('d/m/Y') }}</td>
                    <td><span class="uk-label" style="background:{{ $prStatusColors[$pr->status]['bg'] }}; color:{{ $prStatusColors[$pr->status]['color'] }};">{{ \App\Models\PurchaseRequest::STATUSES[$pr->status] }}</span></td>
                    <td class="uk-text-right">
                        <a href="{{ route('admin.demandes-achat.show', $pr) }}" style="font-weight:600; color:#1D8A4E;">Voir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucune demande d'achat.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $purchaseRequests->links() }}</div>
@endsection
