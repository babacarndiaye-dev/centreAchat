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
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'unit' => $product->unit,
                'price' => (float) $product->price,
                'promo_price' => $product->promo_price ? (float) $product->promo_price : null,
                'professional_price' => $product->professional_price ? (float) $product->professional_price : null,
                'is_new' => (bool) $product->is_new,
                'is_on_promo' => $product->isOnPromo(),
                'is_best_seller' => $product->isBestSeller(),
                'in_stock' => $product->inStock(),
                'stock_quantity' => $product->stock_quantity,
                'stock_alert_threshold' => $product->stock_alert_threshold,
                'rating' => $product->averageRating(),
                'category_name' => $product->category?->name,
                'producer_name' => $product->producer?->name,
                'image' => $product->images->first()?->path,
                'is_wishlisted' => true,
            ])
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
