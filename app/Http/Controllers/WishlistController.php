<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WishlistController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $products = Product::with(['images', 'category', 'producer'])
            ->whereHas('wishlists', fn ($q) => $q->where('user_id', $user->id))
            ->get()
            ->map(fn (Product $product) => $product->toCard($user))
            ->values();

        return Inertia::render('Account/Wishlist', [
            'products' => $products,
            'showProPrice' => $user->isApprovedB2B(),
        ]);
    }

    public function toggle(Product $product): RedirectResponse
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->first();

        if ($wishlist) {
            $wishlist->delete();
            $message = 'Retiré de vos favoris.';
        } else {
            Wishlist::create(['user_id' => Auth::id(), 'product_id' => $product->id]);
            $message = 'Ajouté à vos favoris.';
        }

        return back()->with('success', $message);
    }
}
