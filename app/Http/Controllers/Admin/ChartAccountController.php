<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChartAccountController extends Controller
{
    public function index()
    {
        $accounts = ChartAccount::orderBy('code')->get()->groupBy('class')->map(fn ($group) => $group->map(fn (ChartAccount $account) => [
            'id' => $account->id,
            'code' => $account->code,
            'name' => $account->name,
            'class' => $account->class,
            'is_active' => $account->is_active,
        ])->values());

        return Inertia::render('Admin/Comptabilite/ChartAccounts', [
            'accounts' => $accounts,
            'classes' => ChartAccount::CLASSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:chart_of_accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'class' => ['required', 'integer', 'min:1', 'max:7'],
        ]);

        ChartAccount::create($data);

        return back()->with('success', 'Compte créé.');
    }

    public function update(Request $request, ChartAccount $chartAccount): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $chartAccount->update($data);

        return back()->with('success', 'Compte mis à jour.');
    }

    public function destroy(ChartAccount $chartAccount): RedirectResponse
    {
        if ($chartAccount->lines()->exists()) {
            return back()->with('error', 'Ce compte est utilisé dans des écritures, il ne peut pas être supprimé.');
        }

        $chartAccount->delete();

        return back()->with('success', 'Compte supprimé.');
    }
}
