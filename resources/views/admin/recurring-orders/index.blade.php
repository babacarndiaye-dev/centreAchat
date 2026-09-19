@extends('layouts.admin')

@section('title', 'Commandes récurrentes')

@section('content')
<p class="uk-text-small uk-text-muted">Vue d'ensemble des commandes récurrentes programmées par les clients professionnels. Générées automatiquement chaque jour via <code>php artisan orders:generate-recurring</code>.</p>

<div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
    <table class="uk-table uk-table-divider uk-table-middle" style="margin:0;">
        <thead>
            <tr>
                <th>Client</th>
                <th>Fréquence</th>
                <th>Produits</th>
                <th>Prochaine exécution</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recurringOrders as $ro)
                <tr>
                    <td style="font-weight:600;">{{ $ro->user->name }}</td>
                    <td class="uk-text-muted">{{ \App\Models\RecurringOrder::FREQUENCIES[$ro->frequency] }}</td>
                    <td class="uk-text-muted">{{ $ro->items->pluck('product.name')->join(', ') }}</td>
                    <td>{{ $ro->next_run_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="uk-label" style="{{ $ro->status === 'active' ? 'background:rgba(29,138,78,.12); color:#1D8A4E;' : 'background:rgba(31,35,40,.08); color:rgba(31,35,40,.6);' }}">{{ $ro->status === 'active' ? 'Active' : 'Suspendue' }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="uk-text-center uk-text-muted" style="padding:32px 0;">Aucune commande récurrente.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="uk-margin-top">{{ $recurringOrders->links() }}</div>
@endsection
