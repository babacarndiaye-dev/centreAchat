<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\Analytics\AnalyticsRecorder;
use App\Support\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Product::with(['images', 'category', 'producer'])->where('is_active', true);

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
            AnalyticsRecorder::record('search', ['meta' => ['query' => (string) $search]]);
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

        $products = $query->paginate(12)->withQueryString()->through(fn (Product $p) => $p->toCard($user));
        $categories = Category::where('is_active', true)->orderBy('position')->get(['slug', 'name']);

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'q' => $request->string('q')->toString(),
                'categorie' => $request->string('categorie')->toString(),
                'tri' => (string) $sort,
            ],
        ]);
    }

    public function show(string $slug)
    {
        $user = Auth::user();
        $product = Product::with(['images', 'category', 'producer'])->where('slug', $slug)->where('is_active', true)->firstOrFail();

        AnalyticsRecorder::record('product_view', ['product_id' => $product->id]);

        $related = Product::with(['images', 'producer', 'category'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get()
            ->map(fn (Product $p) => $p->toCard($user));

        $reviews = $product->approvedReviews()->with('user')->get()->map(fn ($review) => [
            'id' => $review->id,
            'rating' => $review->rating,
            'comment' => $review->comment,
            'user_name' => $review->user->name,
            'created_at' => $review->created_at->translatedFormat('d F Y'),
        ]);

        return Inertia::render('Products/Show', [
            'product' => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'reference' => $product->reference,
                'short_description' => $product->short_description,
                'description' => $product->description,
                'origin' => $product->origin,
                'unit' => $product->unit,
                'weight' => $product->weight,
                'price' => (float) $product->price,
                'promo_price' => $product->promo_price ? (float) $product->promo_price : null,
                'professional_price' => $product->professional_price ? (float) $product->professional_price : null,
                'wholesale_price' => $product->wholesale_price ? (float) $product->wholesale_price : null,
                'is_on_promo' => $product->isOnPromo(),
                'in_stock' => $product->inStock(),
                'rating' => $product->averageRating(),
                'reviews_count' => $product->reviewsCount(),
                'price_eur_indicative' => Currency::formatEur($product->currentPrice()),
                'images' => $product->images->map(fn ($img) => $img->path)->values(),
                'category' => $product->category ? ['slug' => $product->category->slug, 'name' => $product->category->name] : null,
                'producer' => $product->producer ? ['slug' => $product->producer->slug, 'name' => $product->producer->name] : null,
                'can_review' => $user && $product->purchasedBy($user) && ! $product->reviewedBy($user),
            ],
            'reviews' => $reviews,
            'related' => $related,
        ]);
    }
}
