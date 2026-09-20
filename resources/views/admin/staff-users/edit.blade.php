@extends('layouts.admin')

@section('title', 'Modifier l\'utilisateur')

@section('content')
<form action="{{ route('admin.utilisateurs.update', $user) }}" method="POST" class="admin-card max-w-xl">
    @csrf
    @method('PATCH')
    <div>
        <label class="label">Nom</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input">
    </div>
    <div class="mt-4">
        <label class="label">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input">
    </div>
    <div class="mt-4">
        <label class="label">Nouveau mot de passe</label>
        <input type="password" name="password" placeholder="Laisser vide pour ne pas changer" class="input">
    </div>
    <div class="mt-4">
        <label class="label">Rôle</label>
        <select name="role_id" class="input">
            <option value="">— Aucun —</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
    <label class="mt-4 flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_admin" value="1" @checked(old('is_admin', $user->is_admin)) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
        Super administrateur (accès complet, ignore les permissions du rôle)
    </label>
    <label class="mt-3 flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active)) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
        Compte actif
    </label>

    <div class="mt-6">
        <button type="submit" class="btn-primary">Enregistrer</button>
        <a href="{{ route('admin.utilisateurs.index') }}" class="ml-3 text-sm font-semibold text-terroir-dark/60">Annuler</a>
    </div>
</form>
@endsection
