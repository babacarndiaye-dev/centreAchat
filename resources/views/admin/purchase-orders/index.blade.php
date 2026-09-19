@extends('layouts.admin')

@section('title', 'Bons de commande')

@section('content')
@php
    $poStatusColors = [
        'brouillon' => ['bg' => 'rgba(240,169,59,.15)', 'color' => '#F0A93B'],
        'envoyee' => ['bg' => 'rgba(240,169,59,.15)', 'color' => '#F0A93B'],
        'confirmee' => ['bg' => 'rgba(31,35,40,.08)', 'color' => 'rgba(31,35,40,.6)'],
        'partiellement_recue' => ['bg' => 'rgba(240,169,59,.15)', 'color' => '#F0A93B'],
        'recue' => ['bg' => 'rgba(29,138,78,.12)', 'color' => '#1D8A4E'],
        'annulee' => ['bg' => 'rgba(232,96,79,.12)', 'color' => '#E8604F'],
    ];
@endphp
<div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap" style="gap:12px;">
    <form method="GET" class="uk-flex" style="gap:8px;">
        <select name="status" class="uk-select" style="max-width:240px;" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach(\App\Models\PurchaseOrder::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('admin.bons-commande.create') }}" class="uk-button uk-button-primary">+ Nouveau bon de commande</a>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>N°</th>
                <th>Fournisseur</th>
                <th>Date</th>
                <th>Statut</th>
                <th class="uk-text-right">Total</th>
                <th class="uk-text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseOrders as $po)
                <tr>
                    <td style="font-weight:600;">{{ $po->order_number }}</td>
                    <td>{{ $po->supplier->name }}</td>
                    <td class="uk-text-muted">{{ $po->order_date->format('d/m/Y') }}</td>
                    <td><span class="uk-label" style="background:{{ $poStatusColors[$po->status]['bg'] }}; color:{{ $poStatusColors[$po->status]['color'] }};">{{ \App\Models\PurchaseOrder::STATUSES[$po->status] }}</span></td>
                    <td class="uk-text-right" style="font-weight:600;">{{ number_format($po->total, 0, ',', ' ') }} FCFA</td>
                    <td class="uk-text-right">
                        <a href="{{ route('admin.bons-commande.show', $po) }}" style="font-weight:600; color:#1D8A4E;">Voir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucun bon de commande.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $purchaseOrders->links() }}</div>
@endsection
