<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Ticket {{ $order->order_number }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="mx-auto max-w-xs bg-white p-6 text-terroir-dark">
    <div class="no-print mb-4 flex items-center justify-between">
        <a href="{{ route('admin.pos.ventes.create') }}" class="text-sm font-semibold text-terroir-green">← Nouvelle vente</a>
        <button onclick="window.print()" class="btn-primary px-4 py-1.5 text-xs">Imprimer</button>
    </div>

    <div class="text-center">
        <p class="font-display text-lg font-semibold text-terroir-green">DIABA HOTEL</p>
        <p class="text-sm text-terroir-dark/50">{{ \App\Models\Setting::get('address') }}</p>
        <p class="text-sm text-terroir-dark/50">{{ \App\Models\Setting::get('phone') }}</p>
    </div>

    <div class="mt-3 border-t border-dashed border-terroir-dark/30 pt-3 text-sm">
        <p>Ticket : {{ $order->order_number }}</p>
        <p>Date : {{ $order->created_at->format('d/m/Y H:i') }}</p>
        <p>Client : {{ $order->customer_name }}</p>
    </div>

    <table class="mt-3 w-full border-t border-dashed border-terroir-dark/30 pt-2 text-sm">
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td class="py-1" colspan="2">{{ $item->product_name }}</td>
                </tr>
                <tr class="text-terroir-dark/50">
                    <td>{{ $item->quantity }} × {{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                    <td class="text-right font-semibold text-terroir-dark">{{ number_format($item->total, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3 flex justify-between border-t border-dashed border-terroir-dark/30 pt-2 text-base font-bold text-terroir-green">
        <span>Total</span><span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
    </div>

    <div class="mt-3 border-t border-dashed border-terroir-dark/30 pt-2 text-sm">
        @foreach($order->posPayments as $payment)
            <div class="flex justify-between"><span>{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</span><span>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span></div>
        @endforeach
    </div>

    <p class="mt-4 text-center text-sm text-terroir-dark/50">Merci de votre confiance !</p>
</body>
</html>
