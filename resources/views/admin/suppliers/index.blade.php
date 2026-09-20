@extends('layouts.admin')

@section('title', 'Fournisseurs')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, société, téléphone..." class="input max-w-xs">
        <select name="status" class="input max-w-[180px]" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach(\App\Models\Supplier::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-outline">Filtrer</button>
    </form>
    <a href="{{ route('admin.fournisseurs.create') }}" class="btn-primary">
        <span class="material-symbols-outlined text-lg">add</span>
        Nouveau fournisseur
    </a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Nom</th>
                <th>Contact</th>
                <th>Ville / Région</th>
                <th>Statut</th>
                <th class="text-right">Solde dû</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suppliers as $supplier)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $supplier->name }}<br><span class="text-xs text-terroir-dark/50">{{ $supplier->company_name }}</span></td>
                    <td class="text-terroir-dark/60">{{ $supplier->phone }}</td>
                    <td class="text-terroir-dark/60">{{ $supplier->city }} {{ $supplier->region ? '('.$supplier->region.')' : '' }}</td>
                    <td><span class="{{ $supplier->statusBadgeClass() }}">{{ \App\Models\Supplier::STATUSES[$supplier->status] }}</span></td>
                    <td class="text-right font-semibold {{ $supplier->balance() > 0 ? 'text-terroir-terracotta' : '' }}">{{ number_format($supplier->balance(), 0, ',', ' ') }} FCFA</td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.fournisseurs.show', $supplier) }}" class="admin-link">Fiche</a>
                        <a href="{{ route('admin.fournisseurs.edit', $supplier) }}" class="admin-link ml-3">Modifier</a>
                        <form action="{{ route('admin.fournisseurs.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce fournisseur ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-8 text-center text-terroir-dark/40">Aucun fournisseur.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $suppliers->links() }}</div>
@endsection
