@extends('layouts.admin')

@section('title', 'Commandes')

@section('content')
<form method="GET" class="flex flex-wrap items-center gap-3">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="N° commande, client, téléphone..." class="input max-w-xs">
    <select name="status" class="input max-w-[220px]" onchange="this.form.submit()">
        <option value="">Tous les statuts</option>
        @foreach(\App\Models\Order::STATUSES as $value => $label)
            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-outline">Filtrer</button>
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
            @forelse($orders as $order)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $order->order_number }}</td>
                    <td>{{ $order->customer_name }}<br><span class="text-xs text-terroir-dark/50">{{ $order->customer_phone }}</span></td>
                    <td class="text-terroir-dark/60">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td><span class="{{ $order->statusBadgeClass() }}">{{ \App\Models\Order::STATUSES[$order->status] ?? $order->status }}</span></td>
                    <td class="text-right font-semibold">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.commandes.show', $order) }}" class="admin-link">Voir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-8 text-center text-terroir-dark/40">Aucune commande.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $orders->links() }}</div>
@endsection
