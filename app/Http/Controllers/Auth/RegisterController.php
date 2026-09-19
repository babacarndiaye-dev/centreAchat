<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'user_type' => ['required', 'in:particulier,professionnel,hotel,restaurant,entreprise,institution,touriste,revendeur'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'business_registration_number' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'user_type' => $data['user_type'],
            'company_name' => $data['company_name'] ?? null,
            'business_registration_number' => $data['business_registration_number'] ?? null,
            'b2b_status' => in_array($data['user_type'], User::B2B_TYPES, true) ? 'en_attente' : 'non_applicable',
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        if ($user->b2b_status === 'en_attente') {
            return redirect()->route('compte.index')->with('success', 'Votre compte professionnel a été créé et est en attente de validation par notre équipe.');
        }

        return redirect()->route('compte.index');
    }
}
