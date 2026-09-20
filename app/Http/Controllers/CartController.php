<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        return Inertia::render('Cart/Index', [
            'items' => Cart::items(),
            'subtotal' => Cart::subtotal(),
        ]);
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        Cart::add($product->id, $data['quantity'] ?? 1);

        return back()->with('success', "« {$product->name} » a été ajouté au panier.");
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:500'],
        ]);

        Cart::update($product->id, $data['quantity']);

        return back()->with('success', 'Panier mis à jour.');
    }

    public function remove(Product $product): RedirectResponse
    {
        Cart::remove($product->id);

        return back()->with('success', 'Produit retiré du panier.');
    }
}
