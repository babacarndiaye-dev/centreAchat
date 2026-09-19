@extends('layouts.admin')

@section('title', 'Caisse')

@section('content')
<div class="uk-flex uk-flex-between uk-flex-middle">
    <p class="uk-text-small uk-text-muted">Historique des sessions de caisse.</p>
    <a href="{{ route('admin.pos.caisse.create') }}" class="uk-button uk-button-primary">Ouvrir / accéder à la caisse</a>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Ouverte par</th>
                <th>Ouverture</th>
                <th>Fond initial</th>
                <th>Statut</th>
                <th class="uk-text-right">Écart</th>
                <th class="uk-text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registers as $register)
                <tr>
                    <td style="font-weight:600;">{{ $register->openedBy->name }}</td>
                    <td class="uk-text-muted">{{ $register->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ number_format($register->opening_float, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @if($register->status === 'ouverte')
                            <span class="uk-label" style="background:rgba(29,138,78,.12); color:#1D8A4E;">Ouverte</span>
                        @else
                            <span class="uk-label" style="background:rgba(31,35,40,.08); color:rgba(31,35,40,.6);">Fermée</span>
                        @endif
                    </td>
                    <td class="uk-text-right" style="{{ $register->variance() && $register->variance() != 0 ? 'font-weight:600; color:#E8604F;' : '' }}">
                        {{ !is_null($register->variance()) ? number_format($register->variance(), 0, ',', ' ').' FCFA' : '—' }}
                    </td>
                    <td class="uk-text-right">
                        <a href="{{ route('admin.pos.caisse.show', $register) }}" style="font-weight:600; color:#1D8A4E;">Voir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucune session de caisse.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $registers->links() }}</div>
@endsection
