<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    public function index()
    {
        $deliveryZones = DeliveryZone::orderBy('position')->orderBy('name')->get();

        return view('admin.commercial.delivery-zones.index', compact('deliveryZones'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = true;

        DeliveryZone::create($data);

        return back()->with('success', 'Zone de livraison créée.');
    }

    public function update(Request $request, DeliveryZone $deliveryZone): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        $deliveryZone->update($data);

        return back()->with('success', 'Zone de livraison mise à jour.');
    }

    public function destroy(DeliveryZone $deliveryZone): RedirectResponse
    {
        $deliveryZone->delete();

        return back()->with('success', 'Zone de livraison supprimée.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cities' => ['nullable', 'string', 'max:255'],
            'fee' => ['required', 'numeric', 'min:0'],
            'free_above' => ['nullable', 'numeric', 'min:0'],
            'delay_days' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
