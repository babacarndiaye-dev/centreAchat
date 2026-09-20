@extends('layouts.admin')

@section('title', 'Plan comptable')

@section('content')
<div class="admin-card max-w-3xl">
    <h2 class="font-display text-lg font-semibold">Nouveau compte</h2>
    <form action="{{ route('admin.comptabilite.plan-comptable.store') }}" method="POST" class="mt-3 flex flex-wrap gap-3">
        @csrf
        <input type="text" name="code" placeholder="Code (ex : 701)" required class="input w-32">
        <input type="text" name="name" placeholder="Intitulé" required class="input min-w-[200px] flex-1">
        <select name="class" required class="input w-40">
            @foreach(\App\Models\ChartAccount::CLASSES as $value => $label)
                <option value="{{ $value }}">Classe {{ $value }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary w-full justify-center">Ajouter</button>
    </form>
</div>

@foreach(\App\Models\ChartAccount::CLASSES as $classNum => $classLabel)
    @if(isset($accounts[$classNum]))
        <div class="admin-card mt-6 overflow-x-auto p-0">
            <div class="bg-terroir-cream px-5 py-3 font-display text-sm font-semibold text-terroir-green">{{ $classLabel }}</div>
            <table class="admin-table">
                <tbody>
                    @foreach($accounts[$classNum] as $account)
                        <tr>
                            <td class="pl-6">
                                <form action="{{ route('admin.comptabilite.plan-comptable.update', $account) }}" method="POST" class="flex items-center gap-3">
                                    @csrf @method('PATCH')
                                    <span class="w-16 font-mono font-semibold text-terroir-dark/70">{{ $account->code }}</span>
                                    <input type="text" name="name" value="{{ $account->name }}" class="input flex-1">
                                    <label class="flex items-center gap-1 whitespace-nowrap text-sm">
                                        <input type="checkbox" name="is_active" value="1" @checked($account->is_active) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
                                        Actif
                                    </label>
                                    <button type="submit" class="text-sm font-semibold text-terroir-green">Enregistrer</button>
                                </form>
                            </td>
                            <td class="pr-6 text-right">
                                <form action="{{ route('admin.comptabilite.plan-comptable.destroy', $account) }}" method="POST" onsubmit="return confirm('Supprimer ce compte ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="admin-link-danger bg-transparent">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endforeach
@endsection
