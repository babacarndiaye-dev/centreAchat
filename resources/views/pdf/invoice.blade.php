<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<style>
    body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #101818; margin: 0; }
    .header { background-color: #101818; padding: 24px 32px; }
    .header td { color: #ffffff; vertical-align: middle; }
    .header .brand { font-size: 20px; font-weight: bold; }
    .header .doctype { font-size: 16px; text-align: right; color: #1DBF63; font-weight: bold; text-transform: uppercase; }
    .accent { height: 4px; background-color: #009C4A; font-size: 0; line-height: 0; }
    .content { padding: 28px 32px; }
    .meta-table { width: 100%; margin-bottom: 24px; }
    .meta-table td { vertical-align: top; padding: 0; }
    .meta-title { font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; color: #8A8F98; margin: 0 0 4px; }
    .meta-value { font-size: 13px; color: #101818; line-height: 1.5; }
    table.items { width: 100%; border-collapse: collapse; margin-top: 12px; }
    table.items th { background-color: #F0F0E8; color: #009C4A; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.03em; }
    table.items th.num, table.items td.num { text-align: right; }
    table.items td { padding: 8px 10px; border-bottom: 1px solid #EDEAE0; font-size: 12px; }
    .totals { width: 100%; margin-top: 16px; }
    .totals td { padding: 4px 10px; font-size: 12px; }
    .totals .label { text-align: right; color: #6B726D; }
    .totals .value { text-align: right; width: 110px; }
    .totals .grand td { border-top: 2px solid #009C4A; font-size: 14px; font-weight: bold; color: #009C4A; padding-top: 8px; }
    .footer { padding: 20px 32px; color: #8A8F98; font-size: 10px; border-top: 1px solid #EDEAE0; margin-top: 24px; }
</style>
</head>
<body>
    <table role="presentation" width="100%" class="header" cellpadding="0" cellspacing="0">
        <tr>
            <td class="brand">{{ $company['name'] }}</td>
            <td class="doctype">{{ $documentType }}<br><span style="color:#ffffff; font-size:12px; font-weight:normal;">{{ $documentNumber }}</span></td>
        </tr>
    </table>
    <div class="accent">&nbsp;</div>

    <div class="content">
        <table class="meta-table" cellpadding="0" cellspacing="0">
            <tr>
                <td width="50%">
                    <p class="meta-title">Émis par</p>
                    <p class="meta-value">
                        {{ $company['name'] }}<br>
                        @if($company['address']) {{ $company['address'] }}<br> @endif
                        @if($company['phone']) {{ $company['phone'] }}<br> @endif
                        @if($company['email']) {{ $company['email'] }} @endif
                    </p>
                </td>
                <td width="50%">
                    <p class="meta-title">Destinataire</p>
                    <p class="meta-value">
                        {{ $client['name'] }}<br>
                        @if(!empty($client['company'])) {{ $client['company'] }}<br> @endif
                        @if(!empty($client['email'])) {{ $client['email'] }}<br> @endif
                        @if(!empty($client['phone'])) {{ $client['phone'] }} @endif
                    </p>
                </td>
            </tr>
            <tr>
                <td style="padding-top:14px;">
                    <p class="meta-title">Date</p>
                    <p class="meta-value">{{ $date }}</p>
                </td>
                @if($dueLabel)
                <td style="padding-top:14px;">
                    <p class="meta-title">{{ $dueLabel }}</p>
                    <p class="meta-value">{{ $dueDate }}</p>
                </td>
                @endif
            </tr>
        </table>

        <table class="items" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="num">Qté</th>
                    <th class="num">Prix unitaire</th>
                    <th class="num">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td class="num">{{ $item['quantity'] }}</td>
                    <td class="num">{{ number_format((float) $item['unit_price'], 0, ',', ' ').' FCFA' }}</td>
                    <td class="num">{{ number_format((float) $item['total'], 0, ',', ' ').' FCFA' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">Sous-total</td>
                <td class="value">{{ number_format((float) $subtotal, 0, ',', ' ').' FCFA' }}</td>
            </tr>
            @if($deliveryFee)
            <tr>
                <td class="label">Livraison</td>
                <td class="value">{{ number_format((float) $deliveryFee, 0, ',', ' ').' FCFA' }}</td>
            </tr>
            @endif
            @if($taxAmount)
            <tr>
                <td class="label">Taxe</td>
                <td class="value">{{ number_format((float) $taxAmount, 0, ',', ' ').' FCFA' }}</td>
            </tr>
            @endif
            @if($discountAmount)
            <tr>
                <td class="label">Remise</td>
                <td class="value">-{{ number_format((float) $discountAmount, 0, ',', ' ').' FCFA' }}</td>
            </tr>
            @endif
            <tr class="grand">
                <td class="label">Total</td>
                <td class="value">{{ number_format((float) $total, 0, ',', ' ').' FCFA' }}</td>
            </tr>
        </table>

        @if($notes)
        <p class="meta-title" style="margin-top:20px;">Notes</p>
        <p class="meta-value">{{ $notes }}</p>
        @endif
    </div>

    <div class="footer">
        {{ $company['name'] }} @if($company['address']) — {{ $company['address'] }} @endif
    </div>
</body>
</html>
