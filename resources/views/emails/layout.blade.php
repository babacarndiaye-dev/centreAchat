@php
    $logoPath = \App\Models\Setting::get('logo_path');
    $siteName = \App\Models\Setting::get('site_name') ?: "Central d'Achat";
    $address = \App\Models\Setting::get('address', 'Rond-Point Malicounda, Mbour – Sénégal');
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ?? $siteName }}</title>
</head>
<body style="margin:0; padding:0; background-color:#F2F0EA; font-family:Georgia, 'Times New Roman', serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F2F0EA; padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 12px 32px rgba(31,35,40,0.08);">

                <tr>
                    <td style="background-color:#1D8A4E; padding:28px 32px; text-align:center;">
                        @if($logoPath)
                            <img src="{{ asset('fichiers/'.$logoPath) }}" alt="{{ $siteName }}" width="48" height="48" style="border-radius:50%; object-fit:cover; display:block; margin:0 auto 10px;">
                        @endif
                        <span style="font-family:Georgia, 'Times New Roman', serif; font-size:20px; font-weight:bold; color:#ffffff; letter-spacing:0.02em;">{{ $siteName }}</span>
                    </td>
                </tr>

                <tr>
                    <td style="height:4px; background-color:#F0A93B; line-height:4px; font-size:0;">&nbsp;</td>
                </tr>

                <tr>
                    <td style="padding:40px 36px 32px;">
                        @isset($title)
                            <h1 style="margin:0 0 18px; font-family:Georgia, 'Times New Roman', serif; font-size:22px; font-weight:normal; color:#1F2328;">{{ $title }}</h1>
                        @endisset

                        <div style="font-family:'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:15px; line-height:1.7; color:#3D4249;">
                            {!! nl2br(e($body)) !!}
                        </div>

                        @isset($ctaUrl)
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin-top:28px;">
                                <tr>
                                    <td style="border-radius:999px; background-color:#1D8A4E;">
                                        <a href="{{ $ctaUrl }}" style="display:inline-block; padding:13px 28px; font-family:'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:14px; font-weight:bold; color:#ffffff; text-decoration:none; border-radius:999px;">{{ $ctaLabel ?? 'Voir les détails' }}</a>
                                    </td>
                                </tr>
                            </table>
                        @endisset
                    </td>
                </tr>

                <tr>
                    <td style="padding:24px 36px; background-color:#F7F8F5; border-top:1px solid #EAE7DF;">
                        <p style="margin:0 0 4px; font-family:'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; color:#8A8F97;">{{ $siteName }} · {{ $address }}</p>
                        <p style="margin:0; font-family:'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:11px; color:#B4B8BE;">Cet e-mail vous est envoyé suite à une action sur notre site.</p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
