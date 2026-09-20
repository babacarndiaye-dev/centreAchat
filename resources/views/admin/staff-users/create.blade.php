@extends('layouts.admin')

@section('title', 'Nouvel utilisateur')

@section('content')
<form action="{{ route('admin.utilisateurs.store') }}" method="POST" class="admin-card max-w-xl">
    @csrf
    <div>
        <label class="label">Nom</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="input">
    </div>
    <div class="mt-4">
        <label class="label">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required class="input">
    </div>
    <div class="mt-4">
        <label class="label">Mot de passe</label>
        <input type="password" name="password" required class="input">
    </div>
    <div class="mt-4">
        <label class="label">Rôle</label>
        <select name="role_id" class="input">
            <option value="">— Aucun —</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
    <label class="mt-4 flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_admin" value="1" @checked(old('is_admin')) class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
        Super administrateur (accès complet, ignore les permissions du rôle)
    </label>

    <div class="mt-6">
        <button type="submit" class="btn-primary">Créer</button>
        <a href="{{ route('admin.utilisateurs.index') }}" class="ml-3 text-sm font-semibold text-terroir-dark/60">Annuler</a>
    </div>
</form>
@endsection
