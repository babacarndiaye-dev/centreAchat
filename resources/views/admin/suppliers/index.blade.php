@extends('layouts.admin')

@section('title', 'Fournisseurs')

@section('content')
@php
    $supplierStatusColors = [
        'actif' => ['bg' => 'rgba(29,138,78,.12)', 'color' => '#1D8A4E'],
        'en_attente' => ['bg' => 'rgba(240,169,59,.15)', 'color' => '#F0A93B'],
        'suspendu' => ['bg' => 'rgba(232,96,79,.12)', 'color' => '#E8604F'],
    ];
@endphp
<div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap" style="gap:12px;">
    <form method="GET" class="uk-flex uk-flex-wrap" style="gap:8px;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, société, téléphone..." class="uk-input" style="max-width:20rem;">
        <select name="status" class="uk-select" style="max-width:180px;" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach(\App\Models\Supplier::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="uk-button uk-button-default">Filtrer</button>
    </form>
    <a href="{{ route('admin.fournisseurs.create') }}" class="uk-button uk-button-primary">+ Nouveau fournisseur</a>
</div>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Contact</th>
                <th>Ville / Région</th>
                <th>Statut</th>
                <th class="uk-text-right">Solde dû</th>
                <th class="uk-text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suppliers as $supplier)
                <tr>
                    <td style="font-weight:600;">{{ $supplier->name }}<br><span class="uk-text-small uk-text-muted">{{ $supplier->company_name }}</span></td>
                    <td class="uk-text-muted">{{ $supplier->phone }}</td>
                    <td class="uk-text-muted">{{ $supplier->city }} {{ $supplier->region ? '('.$supplier->region.')' : '' }}</td>
                    <td>
                        @php $badge = $supplierStatusColors[$supplier->status]; @endphp
                        <span class="uk-label" style="background:{{ $badge['bg'] }}; color:{{ $badge['color'] }};">{{ \App\Models\Supplier::STATUSES[$supplier->status] }}</span>
                    </td>
                    <td class="uk-text-right" style="font-weight:600; {{ $supplier->balance() > 0 ? 'color:#E8604F;' : '' }}">{{ number_format($supplier->balance(), 0, ',', ' ') }} FCFA</td>
                    <td class="uk-text-right">
                        <a href="{{ route('admin.fournisseurs.show', $supplier) }}" style="font-weight:600; color:#1D8A4E;">Fiche</a>
                        <a href="{{ route('admin.fournisseurs.edit', $supplier) }}" style="margin-left:12px; font-weight:600; color:#1D8A4E;">Modifier</a>
                        <form action="{{ route('admin.fournisseurs.destroy', $supplier) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce fournisseur ?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="margin-left:12px; font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucun fournisseur.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $suppliers->links() }}</div>
@endsection
