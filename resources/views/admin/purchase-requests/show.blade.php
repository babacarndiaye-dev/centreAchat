@extends('layouts.admin')

@section('title', $purchaseRequest->reference)

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
<div class="uk-flex uk-flex-wrap uk-flex-middle uk-flex-between" style="gap:16px;">
    <div>
        <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.5rem;">{{ $purchaseRequest->reference }}</h2>
        <p class="uk-text-small uk-text-muted">Demandée par {{ $purchaseRequest->requester?->name ?? '—' }} le {{ $purchaseRequest->created_at->format('d/m/Y') }}</p>
    </div>
    <span class="uk-label" style="background:{{ $prStatusColors[$purchaseRequest->status]['bg'] }}; color:{{ $prStatusColors[$purchaseRequest->status]['color'] }}; padding:8px 16px; font-size:.875rem;">{{ \App\Models\PurchaseRequest::STATUSES[$purchaseRequest->status] }}</span>
</div>

<div class="uk-grid-small uk-margin-top" uk-grid>
    <div class="uk-width-2-3@l">
        <div class="uk-card uk-card-default" style="padding:24px;">
            <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Produits demandés</h3>
            <table class="uk-table uk-table-divider uk-table-middle uk-margin-small-top">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th class="uk-text-right">Quantité demandée</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseRequest->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td class="uk-text-right" style="font-weight:600;">{{ $item->quantity }} {{ $item->product->unit }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($purchaseRequest->reason)
                <div class="uk-margin-top" style="border-radius:8px; background:#F7F8F5; padding:16px; font-size:.875rem;">
                    <strong>Motif :</strong> {{ $purchaseRequest->reason }}
                </div>
            @endif

            @if($purchaseRequest->purchaseOrders->isNotEmpty())
                <div class="uk-margin-top" style="border-top:1px solid rgba(31,35,40,.08); padding-top:24px;">
                    <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Bons de commande liés</h3>
                    <ul class="uk-margin-small-top uk-list" style="font-size:.875rem; display:flex; flex-direction:column; gap:8px;">
                        @foreach($purchaseRequest->purchaseOrders as $po)
                            <li><a href="{{ route('admin.bons-commande.show', $po) }}" style="font-weight:600; color:#1D8A4E;">{{ $po->order_number }}</a> — {{ $po->supplier->name }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="uk-width-1-3@l">
        <div class="uk-card uk-card-default" style="padding:24px;">
            <h3 style="font-family:'Fraunces',serif; font-weight:600; font-size:1rem;">Actions</h3>
            <div class="uk-margin-top" style="display:flex; flex-direction:column; gap:12px;">
                @if($purchaseRequest->status === 'brouillon')
                    <form action="{{ route('admin.demandes-achat.submit', $purchaseRequest) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="uk-button uk-button-primary uk-width-1-1">Soumettre pour validation</button>
                    </form>
                @endif

                @if($purchaseRequest->status === 'en_attente_validation')
                    <form action="{{ route('admin.demandes-achat.validate', $purchaseRequest) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="uk-button uk-button-primary uk-width-1-1">Valider la demande</button>
                    </form>
                    <form action="{{ route('admin.demandes-achat.reject', $purchaseRequest) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="uk-button uk-button-default uk-width-1-1">Rejeter</button>
                    </form>
                @endif

                @if($purchaseRequest->status === 'validee')
                    <a href="{{ route('admin.bons-commande.create', ['demande' => $purchaseRequest->id]) }}" class="uk-button uk-button-primary uk-width-1-1">Créer le bon de commande</a>
                @endif

                @if(in_array($purchaseRequest->status, ['brouillon', 'en_attente_validation']))
                    <form action="{{ route('admin.demandes-achat.destroy', $purchaseRequest) }}" method="POST" onsubmit="return confirm('Supprimer cette demande ?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="font-size:.875rem; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Supprimer la demande</button>
                    </form>
                @endif
            </div>
        </div>

        @if($purchaseRequest->validator)
            <div class="uk-card uk-card-default uk-margin-top" style="padding:24px; font-size:.875rem;">
                <p style="color:rgba(31,35,40,.5);">{{ $purchaseRequest->status === 'rejetee' ? 'Rejetée' : 'Validée' }} par</p>
                <p class="uk-margin-small-top" style="font-weight:600;">{{ $purchaseRequest->validator->name }}</p>
                <p style="color:rgba(31,35,40,.5);">{{ optional($purchaseRequest->validated_at)->format('d/m/Y H:i') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
