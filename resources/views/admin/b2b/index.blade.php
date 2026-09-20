@extends('layouts.admin')

@section('title', 'Clients professionnels')

@section('content')
<form method="GET" class="flex gap-2">
    <select name="status" class="input max-w-[240px]" onchange="this.form.submit()">
        <option value="">Tous les statuts</option>
        <option value="en_attente" @selected(request('status') === 'en_attente')>En attente</option>
        <option value="valide" @selected(request('status') === 'valide')>Validé</option>
        <option value="refuse" @selected(request('status') === 'refuse')>Refusé</option>
    </select>
</form>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Client</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Plafond crédit</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">
                        {{ $client->name }}<br>
                        <span class="text-xs font-normal text-terroir-dark/50">{{ $client->company_name }} — {{ $client->email }}</span>
                        @if($client->business_registration_number)
                            <br><span class="text-xs font-normal text-terroir-dark/50">NINEA/RCCM : {{ $client->business_registration_number }}</span>
                        @endif
                    </td>
                    <td class="text-terroir-dark/60">{{ ucfirst($client->user_type) }}</td>
                    <td>
                        @php
                            $badgeClass = match ($client->b2b_status) {
                                'valide' => 'admin-badge-success',
                                'refuse' => 'admin-badge-danger',
                                default => 'admin-badge-warning',
                            };
                        @endphp
                        <span class="{{ $badgeClass }}">{{ ucfirst($client->b2b_status) }}</span>
                    </td>
                    <td>
                        <form action="{{ route('admin.b2b.credit', $client) }}" method="POST" class="flex items-center gap-2">
                            @csrf @method('PATCH')
                            <input type="number" step="0.01" name="credit_limit" value="{{ $client->credit_limit }}" placeholder="0" class="input w-32">
                            <button type="submit" class="text-sm font-semibold text-terroir-green">OK</button>
                        </form>
                    </td>
                    <td class="pr-6 text-right">
                        @if($client->b2b_status === 'en_attente')
                            <form action="{{ route('admin.b2b.approve', $client) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="admin-link bg-transparent">Valider</button>
                            </form>
                            <form action="{{ route('admin.b2b.reject', $client) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="admin-link-danger ml-3 bg-transparent">Refuser</button>
                            </form>
                        @elseif($client->b2b_status === 'refuse')
                            <form action="{{ route('admin.b2b.approve', $client) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="admin-link bg-transparent">Valider</button>
                            </form>
                        @else
                            <span class="text-terroir-dark/40">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-terroir-dark/40">Aucun client professionnel.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $clients->links() }}</div>
@endsection
