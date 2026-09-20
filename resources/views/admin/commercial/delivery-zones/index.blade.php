@extends('layouts.admin')

@section('title', 'Zones de livraison')

@section('content')
<div class="admin-card max-w-3xl">
    <h2 class="font-display text-lg font-semibold">Nouvelle zone de livraison</h2>
    <form action="{{ route('admin.commercial.zones.store') }}" method="POST" class="mt-3 flex flex-wrap gap-3">
        @csrf
        <input type="text" name="name" placeholder="Nom (ex : Mbour centre)" required class="input min-w-[200px] flex-1">
        <input type="text" name="cities" placeholder="Villes couvertes" class="input w-40">
        <input type="number" step="0.01" name="fee" placeholder="Tarif FCFA" required class="input w-32">
        <input type="number" name="delay_days" placeholder="Délai (jours)" class="input w-32">
        <button type="submit" class="btn-primary w-full justify-center">Ajouter</button>
    </form>

    <div class="mt-8 flex flex-col gap-2">
        @foreach($deliveryZones as $zone)
            <div class="flex flex-wrap items-center gap-3 rounded-lg bg-terroir-cream px-4 py-3">
                <form action="{{ route('admin.commercial.zones.update', $zone) }}" method="POST" class="flex flex-1 flex-wrap items-center gap-2">
                    @csrf @method('PATCH')
                    <input type="text" name="name" value="{{ $zone->name }}" class="input min-w-[140px] flex-1">
                    <input type="text" name="cities" value="{{ $zone->cities }}" placeholder="Villes" class="input w-40">
                    <input type="number" step="0.01" name="fee" value="{{ $zone->fee }}" class="input w-28">
                    <input type="number" step="0.01" name="free_above" value="{{ $zone->free_above }}" placeholder="Gratuit dès" class="input w-32">
                    <input type="number" name="delay_days" value="{{ $zone->delay_days }}" placeholder="Jours" class="input w-20">
                    <label class="flex items-center gap-1 whitespace-nowrap text-sm">
                        <input type="checkbox" name="is_active" value="1" @checked($zone->is_active) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
                        Actif
                    </label>
                    <button type="submit" class="text-sm font-semibold text-terroir-green">Enregistrer</button>
                </form>
                <form action="{{ route('admin.commercial.zones.destroy', $zone) }}" method="POST" onsubmit="return confirm('Supprimer cette zone ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="admin-link-danger bg-transparent">Supprimer</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
