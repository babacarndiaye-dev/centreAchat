@php($assigned = $assignedPermissions ?? [])

<div class="admin-card max-w-5xl">
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="label">Nom du rôle</label>
            <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" required
                {{ isset($role) && $role->is_system ? 'readonly' : '' }}
                class="input">
        </div>
        <div>
            <label class="label">Description</label>
            <input type="text" name="description" value="{{ old('description', $role->description ?? '') }}" class="input">
        </div>
    </div>
</div>

<div class="admin-card mt-6 max-w-5xl overflow-x-auto p-0">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="pl-6">Module</th>
                @foreach(\App\Support\Permissions::ACTIONS as $actionLabel)
                    <th class="text-center">{{ $actionLabel }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach(\App\Support\Permissions::MODULES as $moduleKey => $moduleLabel)
                <tr>
                    <td class="pl-6 font-semibold text-terroir-dark">{{ $moduleLabel }}</td>
                    @foreach(\App\Support\Permissions::ACTIONS as $actionKey => $actionLabel)
                        @php($permission = "{$moduleKey}.{$actionKey}")
                        <td class="text-center">
                            <input type="checkbox" name="permissions[]" value="{{ $permission }}"
                                @checked(in_array($permission, old('permissions', $assigned)))
                                class="rounded border-terroir-green/30 text-terroir-green focus:ring-terroir-green/20">
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6 max-w-5xl">
    <button type="submit" class="btn-primary">Enregistrer</button>
    <a href="{{ route('admin.roles.index') }}" class="ml-3 text-sm font-semibold text-terroir-dark/60 hover:text-terroir-dark">Annuler</a>
</div>
