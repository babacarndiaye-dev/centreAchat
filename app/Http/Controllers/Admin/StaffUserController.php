<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StaffUserController extends Controller
{
    public function index()
    {
        $users = User::with('role')
            ->where(function ($query) {
                $query->where('is_admin', true)->orWhereNotNull('role_id');
            })
            ->orderBy('name')
            ->get();

        return view('admin.staff-users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.staff-users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', Password::defaults()],
            'role_id' => ['nullable', 'exists:roles,id'],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'] ?? null,
            'is_admin' => $request->boolean('is_admin'),
            'is_active' => true,
            'user_type' => 'staff',
        ]);

        return redirect()->route('admin.utilisateurs.index')->with('success', 'Utilisateur créé.');
    }

    public function edit(User $utilisateur)
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.staff-users.edit', ['user' => $utilisateur, 'roles' => $roles]);
    }

    public function update(Request $request, User $utilisateur): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($utilisateur->id)],
            'password' => ['nullable', Password::defaults()],
            'role_id' => ['nullable', 'exists:roles,id'],
            'is_admin' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $utilisateur->name = $data['name'];
        $utilisateur->email = $data['email'];
        $utilisateur->role_id = $data['role_id'] ?? null;
        $utilisateur->is_admin = $request->boolean('is_admin');
        $utilisateur->is_active = $request->boolean('is_active');

        if (! empty($data['password'])) {
            $utilisateur->password = Hash::make($data['password']);
        }

        $utilisateur->save();

        return redirect()->route('admin.utilisateurs.index')->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(User $utilisateur): RedirectResponse
    {
        if ($utilisateur->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $utilisateur->is_admin = false;
        $utilisateur->role_id = null;
        $utilisateur->is_active = false;
        $utilisateur->save();

        return back()->with('success', "Accès staff retiré pour cet utilisateur.");
    }
}
