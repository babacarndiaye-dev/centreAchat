@extends('layouts.admin')

@section('title', 'Retours')

@section('content')
<div class="uk-flex uk-flex-between uk-flex-middle">
    <p class="uk-text-small uk-text-muted">{{ $returns->total() }} retour(s) enregistré(s)</p>
    <a href="{{ route('admin.pos.retours.create') }}" class="uk-button uk-button-primary">+ Nouveau retour</a>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Commande</th>
                <th>Traité par</th>
                <th>Date</th>
                <th class="uk-text-right">Montant remboursé</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $return)
                <tr>
                    <td style="font-weight:600;">{{ $return->order->order_number }}</td>
                    <td class="uk-text-muted">{{ $return->processedBy?->name }}</td>
                    <td class="uk-text-muted">{{ $return->created_at->format('d/m/Y H:i') }}</td>
                    <td class="uk-text-right" style="font-weight:600; color:#E8604F;">{{ number_format($return->total_refund, 0, ',', ' ') }} FCFA</td>
                </tr>
            @empty
                <tr><td colspan="4" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucun retour.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $returns->links() }}</div>
@endsection
