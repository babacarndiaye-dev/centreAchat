@extends('layouts.admin')

@section('title', 'Modifier l\'utilisateur')

@section('content')
<form action="{{ route('admin.utilisateurs.update', $user) }}" method="POST" class="uk-card uk-card-default" style="max-width:36rem; padding:32px;">
    @csrf
    @method('PATCH')
    <div>
        <label class="uk-form-label">Nom</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="uk-input">
    </div>
    <div class="uk-margin-top">
        <label class="uk-form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="uk-input">
    </div>
    <div class="uk-margin-top">
        <label class="uk-form-label">Nouveau mot de passe</label>
        <input type="password" name="password" placeholder="Laisser vide pour ne pas changer" class="uk-input">
    </div>
    <div class="uk-margin-top">
        <label class="uk-form-label">Rôle</label>
        <select name="role_id" class="uk-select">
            <option value="">— Aucun —</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
    <label class="uk-flex uk-flex-middle uk-text-small uk-margin-top" style="gap:8px;">
        <input type="checkbox" name="is_admin" value="1" @checked(old('is_admin', $user->is_admin)) class="uk-checkbox">
        Super administrateur (accès complet, ignore les permissions du rôle)
    </label>
    <label class="uk-flex uk-flex-middle uk-text-small uk-margin-top" style="gap:8px;">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active)) class="uk-checkbox">
        Compte actif
    </label>

    <div class="uk-margin-top">
        <button type="submit" class="uk-button uk-button-primary">Enregistrer</button>
        <a href="{{ route('admin.utilisateurs.index') }}" style="margin-left:12px; font-size:.875rem; font-weight:600; color:rgba(31,35,40,.6);">Annuler</a>
    </div>
</form>
@endsection
