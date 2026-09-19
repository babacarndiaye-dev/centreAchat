<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $products = Product::with('images')
            ->whereHas('wishlists', fn ($q) => $q->where('user_id', Auth::id()))
            ->get();

        return view('account.wishlist', compact('products'));
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
