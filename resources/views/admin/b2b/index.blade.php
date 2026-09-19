@extends('layouts.admin')

@section('title', 'Clients professionnels')

@section('content')
<form method="GET" class="uk-flex" style="gap:8px;">
    <select name="status" class="uk-select" style="max-width:240px;" onchange="this.form.submit()">
        <option value="">Tous les statuts</option>
        <option value="en_attente" @selected(request('status') === 'en_attente')>En attente</option>
        <option value="valide" @selected(request('status') === 'valide')>Validé</option>
        <option value="refuse" @selected(request('status') === 'refuse')>Refusé</option>
    </select>
</form>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Client</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Plafond crédit</th>
                <th class="uk-text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                <tr>
                    <td style="font-weight:600;">
                        {{ $client->name }}<br>
                        <span class="uk-text-small uk-text-muted" style="font-weight:400;">{{ $client->company_name }} — {{ $client->email }}</span>
                        @if($client->business_registration_number)
                            <br><span class="uk-text-small uk-text-muted" style="font-weight:400;">NINEA/RCCM : {{ $client->business_registration_number }}</span>
                        @endif
                    </td>
                    <td class="uk-text-muted">{{ ucfirst($client->user_type) }}</td>
                    <td>
                        @php $badge = ['valide' => 'background:rgba(29,138,78,.12); color:#1D8A4E;', 'en_attente' => 'background:rgba(240,169,59,.2); color:#8a5a1f;', 'refuse' => 'background:rgba(232,96,79,.1); color:#E8604F;'][$client->b2b_status] ?? ''; @endphp
                        <span class="uk-label" style="{{ $badge }}">{{ ucfirst($client->b2b_status) }}</span>
                    </td>
                    <td>
                        <form action="{{ route('admin.b2b.credit', $client) }}" method="POST" class="uk-flex uk-flex-middle" style="gap:8px;">
                            @csrf @method('PATCH')
                            <input type="number" step="0.01" name="credit_limit" value="{{ $client->credit_limit }}" placeholder="0" class="uk-input" style="width:8rem;">
                            <button type="submit" class="uk-text-small" style="font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;">OK</button>
                        </form>
                    </td>
                    <td class="uk-text-right">
                        @if($client->b2b_status === 'en_attente')
                            <form action="{{ route('admin.b2b.approve', $client) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" style="font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;">Valider</button>
                            </form>
                            <form action="{{ route('admin.b2b.reject', $client) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" style="margin-left:12px; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Refuser</button>
                            </form>
                        @elseif($client->b2b_status === 'refuse')
                            <form action="{{ route('admin.b2b.approve', $client) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" style="font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;">Valider</button>
                            </form>
                        @else
                            <span class="uk-text-muted">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucun client professionnel.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $clients->links() }}</div>
@endsection
