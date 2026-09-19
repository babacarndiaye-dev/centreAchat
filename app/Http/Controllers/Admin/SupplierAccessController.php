<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SupplierAccessController extends Controller
{
    public function store(Request $request, Supplier $supplier): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $temporaryPassword = Str::password(10);

        $user = User::create([
            'name' => $supplier->name,
            'email' => $data['email'],
            'phone' => $supplier->phone,
            'user_type' => 'fournisseur',
            'company_name' => $supplier->company_name,
            'password' => Hash::make($temporaryPassword),
            'is_active' => true,
        ]);

        $supplier->update(['user_id' => $user->id]);

        return back()->with('success', "Accès portail créé. Identifiants : {$data['email']} / {$temporaryPassword} (à transmettre au fournisseur, ce mot de passe ne sera plus affiché).");
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->user) {
            $supplier->user->update(['is_active' => false]);
        }

        $supplier->update(['user_id' => null]);

        return back()->with('success', 'Accès portail révoqué.');
    }
}
