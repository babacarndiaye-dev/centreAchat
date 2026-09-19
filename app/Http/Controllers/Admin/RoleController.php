<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RolePermission;
use App\Support\Permissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
        ]);

        $this->syncPermissions($role, $data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Rôle créé.');
    }

    public function edit(Role $role)
    {
        $assignedPermissions = $role->permissions->pluck('permission')->all();

        return view('admin.roles.edit', compact('role', 'assignedPermissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $this->syncPermissions($role, $data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Rôle mis à jour.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->with('error', 'Ce rôle système ne peut pas être supprimé.');
        }

        if ($role->users()->exists()) {
            return back()->with('error', 'Ce rôle est encore attribué à des utilisateurs.');
        }

        $role->delete();

        return back()->with('success', 'Rôle supprimé.');
    }

    protected function syncPermissions(Role $role, array $permissions): void
    {
        $valid = array_intersect($permissions, Permissions::all());

        $role->permissions()->delete();

        foreach ($valid as $permission) {
            RolePermission::create(['role_id' => $role->id, 'permission' => $permission]);
        }
    }
}
