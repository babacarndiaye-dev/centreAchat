<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Ticket {{ $order->order_number }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print { .no-print { display: none; } }
        body { max-width: 320px; margin: 0 auto; }
    </style>
</head>
<body style="background:#fff; padding:24px; color:#1F2328;">
    <div class="no-print uk-flex uk-flex-between uk-margin-bottom">
        <a href="{{ route('admin.pos.ventes.create') }}" class="uk-text-small" style="color:#1D8A4E;">← Nouvelle vente</a>
        <button onclick="window.print()" class="uk-button uk-button-primary uk-button-small">Imprimer</button>
    </div>

    <div class="uk-text-center">
        <p style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem; color:#1D8A4E;">Central d'Achat</p>
        <p class="uk-text-small uk-text-muted">{{ \App\Models\Setting::get('address') }}</p>
        <p class="uk-text-small uk-text-muted">{{ \App\Models\Setting::get('phone') }}</p>
    </div>

    <div class="uk-margin-small-top uk-text-small" style="border-top:1px dashed rgba(31,35,40,.3); padding-top:12px;">
        <p>Ticket : {{ $order->order_number }}</p>
        <p>Date : {{ $order->created_at->format('d/m/Y H:i') }}</p>
        <p>Client : {{ $order->customer_name }}</p>
    </div>

    <table class="uk-margin-small-top uk-text-small" style="width:100%; border-top:1px dashed rgba(31,35,40,.3); padding-top:8px;">
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td style="padding:4px 0;" colspan="2">{{ $item->product_name }}</td>
                </tr>
                <tr class="uk-text-muted">
                    <td>{{ $item->quantity }} × {{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                    <td class="uk-text-right" style="font-weight:600; color:#1F2328;">{{ number_format($item->total, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="uk-flex uk-flex-between uk-margin-small-top" style="border-top:1px dashed rgba(31,35,40,.3); padding-top:8px; font-size:1rem; font-weight:700; color:#1D8A4E;">
        <span>Total</span><span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
    </div>

    <div class="uk-margin-small-top uk-text-small" style="border-top:1px dashed rgba(31,35,40,.3); padding-top:8px;">
        @foreach($order->posPayments as $payment)
            <div class="uk-flex uk-flex-between"><span>{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</span><span>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span></div>
        @endforeach
    </div>

    <p class="uk-margin-top uk-text-center uk-text-small uk-text-muted">Merci de votre confiance !</p>
</body>
</html>
