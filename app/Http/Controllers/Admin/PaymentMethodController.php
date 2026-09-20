<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('position')->orderBy('name')->get()->map(fn (PaymentMethod $method) => [
            'id' => $method->id,
            'code' => $method->code,
            'name' => $method->name,
            'available_online' => $method->available_online,
            'available_pos' => $method->available_pos,
            'requires_b2b' => $method->requires_b2b,
            'is_active' => $method->is_active,
        ]);

        return Inertia::render('Admin/Commercial/PaymentMethods', ['paymentMethods' => $paymentMethods]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'alpha_dash', 'max:50', 'unique:payment_methods,code'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $data['is_active'] = true;
        $data['available_online'] = true;
        $data['available_pos'] = true;

        PaymentMethod::create($data);

        return back()->with('success', 'Mode de paiement créé.');
    }

    public function update(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'available_online' => ['nullable', 'boolean'],
            'available_pos' => ['nullable', 'boolean'],
            'requires_b2b' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['available_online'] = $request->boolean('available_online');
        $data['available_pos'] = $request->boolean('available_pos');
        $data['requires_b2b'] = $request->boolean('requires_b2b');
        $data['is_active'] = $request->boolean('is_active');

        $paymentMethod->update($data);

        return back()->with('success', 'Mode de paiement mis à jour.');
    }

    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        $paymentMethod->delete();

        return back()->with('success', 'Mode de paiement supprimé.');
    }
}
