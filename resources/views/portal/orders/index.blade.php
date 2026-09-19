@extends('layouts.portal')

@section('title', 'Mes commandes')

@section('content')
<h1 class="font-display text-2xl font-semibold">Mes commandes</h1>

<form method="GET" class="mt-6">
    <select name="status" class="input max-w-[260px]" onchange="this.form.submit()">
        <option value="">Tous les statuts</option>
        @foreach(\App\Models\PurchaseOrder::STATUSES as $value => $label)
            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</form>

<div class="card mt-6 overflow-x-auto p-0">
    <table class="w-full text-sm">
        <thead class="bg-terroir-cream text-left text-terroir-dark/60">
            <tr>
                <th class="px-5 py-3">N°</th>
                <th class="px-5 py-3">Date</th>
                <th class="px-5 py-3">Statut</th>
                <th class="px-5 py-3 text-right">Total</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-terroir-green/10">
            @forelse($purchaseOrders as $po)
                <tr>
                    <td class="px-5 py-3 font-medium">{{ $po->order_number }}</td>
                    <td class="px-5 py-3 text-terroir-dark/60">{{ $po->order_date->format('d/m/Y') }}</td>
                    <td class="px-5 py-3">
                        @if($po->status === 'envoyee')
                            <span class="rounded-full bg-terroir-gold/20 px-3 py-1 text-xs font-medium text-terroir-brown">À confirmer</span>
                        @else
                            <span class="rounded-full bg-terroir-cream px-3 py-1 text-xs font-medium text-terroir-green">{{ \App\Models\PurchaseOrder::STATUSES[$po->status] }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right font-semibold">{{ number_format($po->total, 0, ',', ' ') }} FCFA</td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('portail.commandes.show', $po) }}" class="font-medium text-terroir-green hover:underline">Voir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-terroir-dark/50">Aucune commande.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $purchaseOrders->links() }}</div>
@endsection
