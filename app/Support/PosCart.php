<?php

namespace App\Support;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class PosCart
{
    protected const SESSION_KEY = 'pos_cart';
    protected const CUSTOMER_KEY = 'pos_cart_customer';

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
        Session::forget(self::CUSTOMER_KEY);
    }

    public static function raw(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public static function setCustomer(?int $userId): void
    {
        if ($userId) {
            Session::put(self::CUSTOMER_KEY, $userId);
        } else {
            Session::forget(self::CUSTOMER_KEY);
        }
    }

    public static function customer(): ?User
    {
        $id = Session::get(self::CUSTOMER_KEY);

        return $id ? User::find($id) : null;
    }

    public static function items(): Collection
    {
        $cart = self::raw();

        if (empty($cart)) {
            return collect();
        }

        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $customer = self::customer();

        return collect($cart)->map(function (int $quantity, int $productId) use ($products, $customer) {
            $product = $products->get($productId);

            if (! $product) {
                return null;
            }

            $unitPrice = $product->priceFor($customer, $quantity);

            return (object) [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'price_tier' => $product->priceTierFor($customer, $quantity),
                'total' => $unitPrice * $quantity,
            ];
        })->filter()->values();
    }

    public static function subtotal(): float
    {
        return (float) self::items()->sum('total');
    }
}
