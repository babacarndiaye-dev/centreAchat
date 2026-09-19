<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Notifications\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class B2bController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('user_type', User::B2B_TYPES);

        if ($request->filled('status')) {
            $query->where('b2b_status', $request->string('status'));
        }

        $clients = $query->latest()->paginate(20)->withQueryString();

        return view('admin.b2b.index', compact('clients'));
    }

    public function approve(User $user, NotificationService $notifications): RedirectResponse
    {
        $user->update(['b2b_status' => 'valide']);

        $notifications->send('b2b_compte_valide', [NotificationService::recipientFromUser($user)], [
            'client_nom' => $user->name,
            'compte_type' => ucfirst($user->user_type),
            'compte_lien' => route('compte.index'),
        ]);

        return back()->with('success', 'Compte professionnel validé.');
    }

    public function reject(User $user, NotificationService $notifications): RedirectResponse
    {
        $user->update(['b2b_status' => 'refuse']);

        $notifications->send('b2b_compte_refuse', [NotificationService::recipientFromUser($user)], [
            'client_nom' => $user->name,
            'compte_type' => ucfirst($user->user_type),
        ]);

        return back()->with('success', 'Compte professionnel refusé.');
    }

    public function updateCredit(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $user->update($data);

        return back()->with('success', 'Plafond de crédit mis à jour.');
    }
}
