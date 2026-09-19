<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['images', 'category', 'producer'])->where('is_active', true);

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categorie')) {
            $category = Category::where('slug', $request->string('categorie'))->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->boolean('promo')) {
            $now = now();
            $query->whereNotNull('promo_price')
                ->where(fn ($q) => $q->whereNull('promo_starts_at')->orWhere('promo_starts_at', '<=', $now))
                ->where(fn ($q) => $q->whereNull('promo_ends_at')->orWhere('promo_ends_at', '>=', $now));
        }

        $sort = $request->string('tri', 'recent');
        match ((string) $sort) {
            'prix_asc' => $query->orderBy('price'),
            'prix_desc' => $query->orderByDesc('price'),
            'nom' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('position')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(string $slug)
    {
        $product = Product::with(['images', 'category', 'producer'])->where('slug', $slug)->where('is_active', true)->firstOrFail();

        $related = Product::with(['images', 'producer'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'related'));
    }
}
