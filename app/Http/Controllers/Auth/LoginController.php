<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function show()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request): RedirectResponse|Response
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Identifiants incorrects.'])->onlyInput('email');
        }

        if (! Auth::user()->is_active) {
            Auth::logout();

            return back()->withErrors(['email' => 'Ce compte a été désactivé.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        // admin.dashboard/portail.dashboard are still Blade pages at this stage of the
        // migration — Inertia::location() forces a full browser navigation there instead
        // of an Inertia partial visit (which would fail on a non-Inertia response).
        if (Auth::user()->isStaff()) {
            return Inertia::location(redirect()->intended(route('admin.dashboard')));
        }

        if (Auth::user()->supplier) {
            return Inertia::location(redirect()->intended(route('portail.dashboard')));
        }

        return redirect()->intended(route('compte.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('accueil');
    }
}
