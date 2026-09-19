<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Cart
{
    protected const SESSION_KEY = 'cart';

    public static function add(int $productId, int $quantity = 1): void
    {
        $cart = self::raw();
        $cart[$productId] = ($cart[$productId] ?? 0) + $quantity;
        Session::put(self::SESSION_KEY, $cart);
    }

    public static function update(int $productId, int $quantity): void
    {
        $cart = self::raw();

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    public static function remove(int $productId): void
    {
        $cart = self::raw();
        unset($cart[$productId]);
        Session::put(self::SESSION_KEY, $cart);
    }

    public static function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public static function raw(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public static function count(): int
    {
        return array_sum(self::raw());
    }

    public static function items(): Collection
    {
        $cart = self::raw();

        if (empty($cart)) {
            return collect();
        }

        $products = Product::with('images')->whereIn('id', array_keys($cart))->get()->keyBy('id');
        $user = Auth::user();

        return collect($cart)->map(function (int $quantity, int $productId) use ($products, $user) {
            $product = $products->get($productId);

            if (! $product) {
                return null;
            }

            $unitPrice = $product->priceFor($user, $quantity);

            return (object) [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'price_tier' => $product->priceTierFor($user, $quantity),
                'total' => $unitPrice * $quantity,
            ];
        })->filter()->values();
    }

    public static function subtotal(): float
    {
        return (float) self::items()->sum('total');
    }
}
