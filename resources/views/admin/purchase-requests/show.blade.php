@extends('layouts.admin')

@section('title', $purchaseRequest->reference)

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <h2 class="font-display text-2xl font-semibold">{{ $purchaseRequest->reference }}</h2>
        <p class="text-sm text-terroir-dark/50">Demandée par {{ $purchaseRequest->requester?->name ?? '—' }} le {{ $purchaseRequest->created_at->format('d/m/Y') }}</p>
    </div>
    <span class="{{ $purchaseRequest->statusBadgeClass() }} px-4 py-1.5 text-sm">{{ \App\Models\PurchaseRequest::STATUSES[$purchaseRequest->status] }}</span>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <div class="admin-card">
            <h3 class="font-display text-base font-semibold">Produits demandés</h3>
            <table class="admin-table mt-3">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th class="text-right">Quantité demandée</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseRequest->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td class="text-right font-semibold">{{ $item->quantity }} {{ $item->product->unit }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($purchaseRequest->reason)
                <div class="mt-4 rounded-lg bg-terroir-cream p-4 text-sm">
                    <strong>Motif :</strong> {{ $purchaseRequest->reason }}
                </div>
            @endif

            @if($purchaseRequest->purchaseOrders->isNotEmpty())
                <div class="mt-6 border-t border-terroir-dark/10 pt-6">
                    <h3 class="font-display text-base font-semibold">Bons de commande liés</h3>
                    <ul class="mt-3 flex flex-col gap-2 text-sm">
                        @foreach($purchaseRequest->purchaseOrders as $po)
                            <li><a href="{{ route('admin.bons-commande.show', $po) }}" class="admin-link">{{ $po->order_number }}</a> — {{ $po->supplier->name }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div>
        <div class="admin-card">
            <h3 class="font-display text-base font-semibold">Actions</h3>
            <div class="mt-4 flex flex-col gap-3">
                @if($purchaseRequest->status === 'brouillon')
                    <form action="{{ route('admin.demandes-achat.submit', $purchaseRequest) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-primary w-full justify-center">Soumettre pour validation</button>
                    </form>
                @endif

                @if($purchaseRequest->status === 'en_attente_validation')
                    <form action="{{ route('admin.demandes-achat.validate', $purchaseRequest) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-primary w-full justify-center">Valider la demande</button>
                    </form>
                    <form action="{{ route('admin.demandes-achat.reject', $purchaseRequest) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-outline w-full justify-center">Rejeter</button>
                    </form>
                @endif

                @if($purchaseRequest->status === 'validee')
                    <a href="{{ route('admin.bons-commande.create', ['demande' => $purchaseRequest->id]) }}" class="btn-primary w-full justify-center">Créer le bon de commande</a>
                @endif

                @if(in_array($purchaseRequest->status, ['brouillon', 'en_attente_validation']))
                    <form action="{{ route('admin.demandes-achat.destroy', $purchaseRequest) }}" method="POST" onsubmit="return confirm('Supprimer cette demande ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="admin-link-danger bg-transparent">Supprimer la demande</button>
                    </form>
                @endif
            </div>
        </div>

        @if($purchaseRequest->validator)
            <div class="admin-card mt-6 text-sm">
                <p class="text-terroir-dark/50">{{ $purchaseRequest->status === 'rejetee' ? 'Rejetée' : 'Validée' }} par</p>
                <p class="mt-1.5 font-semibold">{{ $purchaseRequest->validator->name }}</p>
                <p class="text-terroir-dark/50">{{ optional($purchaseRequest->validated_at)->format('d/m/Y H:i') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
