@extends('layouts.admin')

@section('title', 'Taxes')

@section('content')
<div class="admin-card max-w-2xl">
    <h2 class="font-display text-lg font-semibold">Nouveau taux de taxe</h2>
    <form action="{{ route('admin.commercial.taxes.store') }}" method="POST" class="mt-3 flex gap-2">
        @csrf
        <input type="text" name="name" placeholder="Ex : TVA standard" required class="input flex-1">
        <input type="number" step="0.01" name="rate" placeholder="Taux %" required class="input w-32">
        <button type="submit" class="btn-primary">Ajouter</button>
    </form>

    <div class="mt-8 flex flex-col gap-2">
        @foreach($taxRates as $tax)
            <div class="flex items-center justify-between rounded-lg bg-terroir-cream px-4 py-2.5">
                <form action="{{ route('admin.commercial.taxes.update', $tax) }}" method="POST" class="flex flex-1 items-center gap-3">
                    @csrf @method('PATCH')
                    <input type="text" name="name" value="{{ $tax->name }}" class="input flex-1">
                    <input type="number" step="0.01" name="rate" value="{{ $tax->rate }}" class="input w-24">
                    <span class="text-sm">%</span>
                    <label class="flex items-center gap-1 whitespace-nowrap text-sm">
                        <input type="checkbox" name="is_active" value="1" @checked($tax->is_active) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
                        Actif
                    </label>
                    <button type="submit" class="text-sm font-semibold text-terroir-green">Enregistrer</button>
                </form>
                @if($tax->is_default)
                    <span class="admin-badge-success ml-3">Par défaut</span>
                @else
                    <form action="{{ route('admin.commercial.taxes.default', $tax) }}" method="POST" class="ml-3">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-xs font-semibold text-terroir-dark/60">Définir par défaut</button>
                    </form>
                @endif
                <form action="{{ route('admin.commercial.taxes.destroy', $tax) }}" method="POST" onsubmit="return confirm('Supprimer ce taux ?')" class="ml-3">
                    @csrf @method('DELETE')
                    <button type="submit" class="admin-link-danger bg-transparent">Supprimer</button>
                </form>
            </div>
        @endforeach
    </div>
    <p class="mt-4 text-sm text-terroir-dark/50">Le taux "par défaut" est celui appliqué automatiquement au calcul des commandes.</p>
</div>
@endsection
