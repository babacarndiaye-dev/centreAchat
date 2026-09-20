@extends('layouts.admin')

@section('title', 'Retours')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-sm text-terroir-dark/50">{{ $returns->total() }} retour(s) enregistré(s)</p>
    <a href="{{ route('admin.pos.retours.create') }}" class="btn-primary">
        <span class="material-symbols-outlined text-lg">add</span>
        Nouveau retour
    </a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Commande</th>
                <th>Traité par</th>
                <th>Date</th>
                <th class="pr-6 text-right">Montant remboursé</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $return)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $return->order->order_number }}</td>
                    <td class="text-terroir-dark/60">{{ $return->processedBy?->name }}</td>
                    <td class="text-terroir-dark/60">{{ $return->created_at->format('d/m/Y H:i') }}</td>
                    <td class="pr-6 text-right font-semibold text-terroir-terracotta">{{ number_format($return->total_refund, 0, ',', ' ') }} FCFA</td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-8 text-center text-terroir-dark/40">Aucun retour.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $returns->links() }}</div>
@endsection
