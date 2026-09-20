@extends('layouts.admin')

@section('title', 'Caisse')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-sm text-terroir-dark/50">Historique des sessions de caisse.</p>
    <a href="{{ route('admin.pos.caisse.create') }}" class="btn-primary">Ouvrir / accéder à la caisse</a>
</div>

<div class="admin-card mt-6 overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Ouverte par</th>
                <th>Ouverture</th>
                <th>Fond initial</th>
                <th>Statut</th>
                <th class="text-right">Écart</th>
                <th class="pr-6 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registers as $register)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $register->openedBy->name }}</td>
                    <td class="text-terroir-dark/60">{{ $register->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ number_format($register->opening_float, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @if($register->status === 'ouverte')
                            <span class="admin-badge-success">Ouverte</span>
                        @else
                            <span class="admin-badge-neutral">Fermée</span>
                        @endif
                    </td>
                    <td class="text-right {{ $register->variance() && $register->variance() != 0 ? 'font-semibold text-terroir-terracotta' : '' }}">
                        {{ !is_null($register->variance()) ? number_format($register->variance(), 0, ',', ' ').' FCFA' : '—' }}
                    </td>
                    <td class="pr-6 text-right">
                        <a href="{{ route('admin.pos.caisse.show', $register) }}" class="admin-link">Voir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-8 text-center text-terroir-dark/40">Aucune session de caisse.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $registers->links() }}</div>
@endsection
