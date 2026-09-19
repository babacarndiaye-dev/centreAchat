<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $user = Auth::user();

        if (! $product->purchasedBy($user)) {
            return back()->with('error', "Seuls les clients ayant reçu ce produit peuvent laisser un avis.");
        }

        if ($product->reviewedBy($user)) {
            return back()->with('error', 'Vous avez déjà laissé un avis pour ce produit.');
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $product->reviews()->create([
            'user_id' => $user->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return back()->with('success', 'Merci, votre avis a été publié.');
    }
}
