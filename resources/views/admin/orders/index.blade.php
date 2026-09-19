@extends('layouts.admin')

@section('title', 'Commandes')

@section('content')
<form method="GET" class="uk-flex uk-flex-wrap" style="gap:12px;">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="N° commande, client, téléphone..." class="uk-input" style="max-width:20rem;">
    <select name="status" class="uk-select" style="max-width:220px;" onchange="this.form.submit()">
        <option value="">Tous les statuts</option>
        @foreach(\App\Models\Order::STATUSES as $value => $label)
            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="uk-button uk-button-default">Filtrer</button>
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
            @forelse($orders as $order)
                <tr>
                    <td style="font-weight:600;">{{ $order->order_number }}</td>
                    <td>{{ $order->customer_name }}<br><span class="uk-text-small uk-text-muted">{{ $order->customer_phone }}</span></td>
                    <td class="uk-text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td><span class="uk-label" style="background:#F7F8F5; color:#1D8A4E;">{{ \App\Models\Order::STATUSES[$order->status] ?? $order->status }}</span></td>
                    <td class="uk-text-right" style="font-weight:600;">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                    <td class="uk-text-right">
                        <a href="{{ route('admin.commandes.show', $order) }}" style="font-weight:600; color:#1D8A4E;">Voir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucune commande.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $orders->links() }}</div>
@endsection
