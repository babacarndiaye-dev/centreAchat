@extends('layouts.admin')

@section('title', 'Plan comptable')

@section('content')
<div class="uk-card uk-card-default" style="max-width:48rem; padding:32px;">
    <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:1.125rem;">Nouveau compte</h2>
    <form action="{{ route('admin.comptabilite.plan-comptable.store') }}" method="POST" class="uk-grid-small uk-child-width-1-4@s uk-margin-top" uk-grid>
        @csrf
        <div><input type="text" name="code" placeholder="Code (ex : 701)" required class="uk-input"></div>
        <div class="uk-width-1-2@s"><input type="text" name="name" placeholder="Intitulé" required class="uk-input"></div>
        <div>
            <select name="class" required class="uk-select">
                @foreach(\App\Models\ChartAccount::CLASSES as $value => $label)
                    <option value="{{ $value }}">Classe {{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="uk-width-1-1"><button type="submit" class="uk-button uk-button-primary uk-width-1-1">Ajouter</button></div>
    </form>
</div>

@foreach(\App\Models\ChartAccount::CLASSES as $classNum => $classLabel)
    @if(isset($accounts[$classNum]))
        <div class="uk-card uk-card-default uk-margin-top" style="overflow-x:auto; padding:0;">
            <div style="background:#F7F8F5; padding:12px 20px; font-family:'Fraunces',serif; font-weight:600; font-size:0.875rem; color:#1D8A4E;">{{ $classLabel }}</div>
            <table class="uk-table uk-table-divider uk-table-small uk-table-middle" style="margin:0;">
                <tbody>
                    @foreach($accounts[$classNum] as $account)
                        <tr>
                            <td>
                                <form action="{{ route('admin.comptabilite.plan-comptable.update', $account) }}" method="POST" class="uk-flex uk-flex-middle" style="gap:12px;">
                                    @csrf @method('PATCH')
                                    <span style="width:64px; font-family:monospace; font-weight:600; color:rgba(31,35,40,.7);">{{ $account->code }}</span>
                                    <input type="text" name="name" value="{{ $account->name }}" class="uk-input uk-width-expand">
                                    <label class="uk-text-small uk-flex uk-flex-middle" style="gap:4px; white-space:nowrap;">
                                        <input type="checkbox" name="is_active" value="1" @checked($account->is_active) class="uk-checkbox">
                                        Actif
                                    </label>
                                    <button type="submit" style="font-weight:600; color:#1D8A4E; background:none; border:none; cursor:pointer;">Enregistrer</button>
                                </form>
                            </td>
                            <td class="uk-text-right">
                                <form action="{{ route('admin.comptabilite.plan-comptable.destroy', $account) }}" method="POST" onsubmit="return confirm('Supprimer ce compte ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="font-weight:600; color:#E8604F; background:none; border:none; cursor:pointer;">Supprimer</button>
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
