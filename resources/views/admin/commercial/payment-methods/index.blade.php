@extends('layouts.admin')

@section('title', 'Modes de paiement')

@section('content')
<div class="admin-card max-w-3xl">
    <h2 class="font-display text-lg font-semibold">Nouveau mode de paiement</h2>
    <form action="{{ route('admin.commercial.paiements.store') }}" method="POST" class="mt-3 flex gap-2">
        @csrf
        <input type="text" name="code" placeholder="Code (ex : wave)" required class="input w-40">
        <input type="text" name="name" placeholder="Nom affiché (ex : Wave)" required class="input flex-1">
        <button type="submit" class="btn-primary">Ajouter</button>
    </form>

    <div class="mt-8 flex flex-col gap-2">
        @foreach($paymentMethods as $method)
            <div class="flex flex-wrap items-center gap-3 rounded-lg bg-terroir-cream px-4 py-3">
                <form action="{{ route('admin.commercial.paiements.update', $method) }}" method="POST" class="flex flex-1 flex-wrap items-center gap-3">
                    @csrf @method('PATCH')
                    <span class="w-28 font-mono text-sm text-terroir-dark/50">{{ $method->code }}</span>
                    <input type="text" name="name" value="{{ $method->name }}" class="input min-w-[160px] flex-1">
                    <label class="flex items-center gap-1 text-sm"><input type="checkbox" name="available_online" value="1" @checked($method->available_online) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"> Site web</label>
                    <label class="flex items-center gap-1 text-sm"><input type="checkbox" name="available_pos" value="1" @checked($method->available_pos) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"> Caisse (POS)</label>
                    <label class="flex items-center gap-1 text-sm"><input type="checkbox" name="requires_b2b" value="1" @checked($method->requires_b2b) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"> Pro validé uniquement</label>
                    <label class="flex items-center gap-1 text-sm"><input type="checkbox" name="is_active" value="1" @checked($method->is_active) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20"> Actif</label>
                    <button type="submit" class="text-sm font-semibold text-terroir-green">Enregistrer</button>
                </form>
                <form action="{{ route('admin.commercial.paiements.destroy', $method) }}" method="POST" onsubmit="return confirm('Supprimer {{ $method->name }} ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="admin-link-danger bg-transparent">Supprimer</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
