@extends('layouts.admin')

@section('title', 'Commandes récurrentes')

@section('content')
<p class="text-sm text-terroir-dark/50">Vue d'ensemble des commandes récurrentes programmées par les clients professionnels. Générées automatiquement chaque jour via <code>php artisan orders:generate-recurring</code>.</p>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Client</th>
                <th>Fréquence</th>
                <th>Produits</th>
                <th>Prochaine exécution</th>
                <th class="pr-6">Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recurringOrders as $ro)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $ro->user->name }}</td>
                    <td class="text-terroir-dark/60">{{ \App\Models\RecurringOrder::FREQUENCIES[$ro->frequency] }}</td>
                    <td class="text-terroir-dark/60">{{ $ro->items->pluck('product.name')->join(', ') }}</td>
                    <td>{{ $ro->next_run_date->format('d/m/Y') }}</td>
                    <td class="pr-6">
                        <span class="{{ $ro->status === 'active' ? 'admin-badge-success' : 'admin-badge-neutral' }}">{{ $ro->status === 'active' ? 'Active' : 'Suspendue' }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-terroir-dark/40">Aucune commande récurrente.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $recurringOrders->links() }}</div>
@endsection
