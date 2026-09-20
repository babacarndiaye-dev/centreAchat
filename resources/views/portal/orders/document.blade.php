<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bon de commande {{ $purchaseOrder->order_number }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-white p-10 text-terroir-dark">
    <div class="no-print mb-6 text-right">
        <button onclick="window.print()" class="btn-primary">Imprimer / Enregistrer en PDF</button>
    </div>

    <div class="flex items-start justify-between border-b border-terroir-dark/10 pb-6">
        <div>
            <p class="font-display text-2xl font-semibold text-terroir-green">Centrale d'achat</p>
            <p class="mt-1 text-sm text-terroir-dark/60">{{ \App\Models\Setting::get('address', 'Rond-Point Malicounda, Mbour – Sénégal') }}</p>
            <p class="text-sm text-terroir-dark/60">{{ \App\Models\Setting::get('phone') }} — {{ \App\Models\Setting::get('email') }}</p>
        </div>
        <div class="text-right">
            <p class="font-display text-xl font-semibold">Bon de commande</p>
            <p class="mt-1 text-sm text-terroir-dark/60">N° {{ $purchaseOrder->order_number }}</p>
            <p class="text-sm text-terroir-dark/60">Date : {{ $purchaseOrder->order_date->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="mt-6">
        <p class="text-xs font-semibold uppercase tracking-wide text-terroir-dark/50">Fournisseur</p>
        <p class="mt-1 font-medium">{{ $purchaseOrder->supplier->name }}</p>
        <p class="text-sm text-terroir-dark/60">{{ $purchaseOrder->supplier->company_name }}</p>
        <p class="text-sm text-terroir-dark/60">{{ $purchaseOrder->supplier->address }}, {{ $purchaseOrder->supplier->city }}</p>
        <p class="text-sm text-terroir-dark/60">{{ $purchaseOrder->supplier->phone }}</p>
    </div>

    <table class="mt-8 w-full border-collapse text-sm">
        <thead>
            <tr class="border-b-2 border-terroir-dark/20 text-left">
                <th class="py-2">Produit</th>
                <th class="py-2 text-right">Quantité</th>
                <th class="py-2 text-right">Prix unitaire</th>
                <th class="py-2 text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $item)
                <tr class="border-b border-terroir-dark/10">
                    <td class="py-2">{{ $item->product->name }}</td>
                    <td class="py-2 text-right">{{ $item->quantity_ordered }}</td>
                    <td class="py-2 text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                    <td class="py-2 text-right">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4 flex justify-end">
        <div class="w-64 text-right">
            <div class="flex justify-between border-t-2 border-terroir-dark/20 pt-2 text-base font-bold text-terroir-green">
                <span>Total</span><span>{{ number_format($purchaseOrder->total, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>
    </div>

    @if($purchaseOrder->notes)
        <div class="mt-8 text-sm text-terroir-dark/70">
            <p class="font-semibold">Notes :</p>
            <p>{{ $purchaseOrder->notes }}</p>
        </div>
    @endif

    <p class="mt-12 text-xs text-terroir-dark/40">Document généré automatiquement par la plateforme Centrale d'achat.</p>
</body>
</html>
