@extends('layouts.admin')

@section('title', 'Bons de commande')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="flex gap-2">
        <select name="status" class="input max-w-[240px]" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach(\App\Models\PurchaseOrder::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('admin.bons-commande.create') }}" class="btn-primary">
        <span class="material-symbols-outlined text-lg">add</span>
        Nouveau bon de commande
    </a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">N°</th>
                <th>Fournisseur</th>
                <th>Date</th>
                <th>Statut</th>
                <th class="text-right">Total</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseOrders as $po)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $po->order_number }}</td>
                    <td>{{ $po->supplier->name }}</td>
                    <td class="text-terroir-dark/60">{{ $po->order_date->format('d/m/Y') }}</td>
                    <td><span class="{{ $po->statusBadgeClass() }}">{{ \App\Models\PurchaseOrder::STATUSES[$po->status] }}</span></td>
                    <td class="text-right font-semibold">{{ number_format($po->total, 0, ',', ' ') }} FCFA</td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.bons-commande.show', $po) }}" class="admin-link">Voir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-8 text-center text-terroir-dark/40">Aucun bon de commande.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $purchaseOrders->links() }}</div>
@endsection
