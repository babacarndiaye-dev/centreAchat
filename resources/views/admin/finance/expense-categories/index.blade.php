@extends('layouts.admin')

@section('title', 'Catégories de dépenses')

@section('content')
<div class="admin-card max-w-3xl">
    <h2 class="font-display text-lg font-semibold">Nouvelle catégorie</h2>
    <form action="{{ route('admin.categories-depenses.store') }}" method="POST" class="mt-3 flex gap-2">
        @csrf
        <input type="text" name="name" placeholder="Ex : Loyer, Électricité, Carburant..." required class="input flex-1">
        <select name="chart_account_id" class="input max-w-[280px]">
            <option value="">Compte comptable (optionnel)</option>
            @foreach($chartAccounts as $chartAccount)
                <option value="{{ $chartAccount->id }}">{{ $chartAccount->code }} — {{ $chartAccount->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary">Ajouter</button>
    </form>

    <div class="mt-4 flex flex-col gap-2">
        @foreach($categories as $category)
            <div class="flex items-center justify-between rounded-lg bg-terroir-cream px-4 py-2.5">
                <form action="{{ route('admin.categories-depenses.update', $category) }}" method="POST" class="flex flex-1 items-center gap-3">
                    @csrf @method('PATCH')
                    <input type="text" name="name" value="{{ $category->name }}" class="input flex-1">
                    <select name="chart_account_id" class="input max-w-[220px]">
                        <option value="">Compte comptable</option>
                        @foreach($chartAccounts as $chartAccount)
                            <option value="{{ $chartAccount->id }}" @selected($category->chart_account_id === $chartAccount->id)>{{ $chartAccount->code }} — {{ $chartAccount->name }}</option>
                        @endforeach
                    </select>
                    <label class="flex items-center gap-1 whitespace-nowrap text-sm">
                        <input type="checkbox" name="is_active" value="1" @checked($category->is_active) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
                        Actif
                    </label>
                    <button type="submit" class="text-sm font-semibold text-terroir-green">Enregistrer</button>
                </form>
                <form action="{{ route('admin.categories-depenses.destroy', $category) }}" method="POST" onsubmit="return confirm('Supprimer cette catégorie ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="admin-link-danger ml-3 bg-transparent">Supprimer</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
