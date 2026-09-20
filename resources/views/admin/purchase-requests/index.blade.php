@extends('layouts.admin')

@section('title', "Demandes d'achat")

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="flex gap-2">
        <select name="status" class="input max-w-[240px]" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach(\App\Models\PurchaseRequest::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('admin.demandes-achat.create') }}" class="btn-primary">
        <span class="material-symbols-outlined text-lg">add</span>
        Nouvelle demande d'achat
    </a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Référence</th>
                <th>Demandeur</th>
                <th>Date</th>
                <th>Statut</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseRequests as $pr)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $pr->reference }}</td>
                    <td class="text-terroir-dark/60">{{ $pr->requester?->name ?? '—' }}</td>
                    <td class="text-terroir-dark/60">{{ $pr->created_at->format('d/m/Y') }}</td>
                    <td><span class="{{ $pr->statusBadgeClass() }}">{{ \App\Models\PurchaseRequest::STATUSES[$pr->status] }}</span></td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.demandes-achat.show', $pr) }}" class="admin-link">Voir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-terroir-dark/40">Aucune demande d'achat.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $purchaseRequests->links() }}</div>
@endsection
