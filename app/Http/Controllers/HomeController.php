<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Producer;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $categories = Category::where('is_active', true)->whereNull('parent_id')->orderBy('position')->take(4)->get();
        $featuredProducts = Product::with(['images', 'producer', 'category'])->where('is_active', true)->where('is_featured', true)->take(8)->get();
        $newProducts = Product::with(['images', 'producer', 'category'])->where('is_active', true)->where('is_new', true)->take(8)->get();
        $producers = Producer::where('is_active', true)->where('is_featured', true)->take(4)->get();
        $testimonials = Testimonial::where('is_published', true)->take(6)->get();

        $promoProduct = Product::with('images')
            ->where('is_active', true)
            ->whereNotNull('promo_price')
            ->get()
            ->first(fn (Product $product) => $product->isOnPromo());

        return Inertia::render('Home', [
            'heroTitle' => Setting::get('hero_title'),
            'heroSubtitle' => Setting::get('hero_subtitle'),
            'categories' => $categories->map(fn (Category $c) => ['id' => $c->id, 'slug' => $c->slug, 'name' => $c->name]),
            'featuredProducts' => $featuredProducts->map(fn (Product $p) => $p->toCard($user))->values(),
            'newProducts' => $newProducts->map(fn (Product $p) => $p->toCard($user))->values(),
            'slideProducts' => $featuredProducts->filter(fn (Product $p) => $p->mainImage())->take(5)->values()->map(fn (Product $p) => [
                'slug' => $p->slug,
                'name' => $p->name,
                'category_name' => $p->category?->name,
                'price' => $p->currentPrice(),
                'image' => $p->mainImage(),
            ]),
            'promoProduct' => $promoProduct ? [
                'slug' => $promoProduct->slug,
                'name' => $promoProduct->name,
                'short_description' => $promoProduct->short_description,
                'price' => (float) $promoProduct->price,
                'promo_price' => (float) $promoProduct->promo_price,
                'promo_ends_at' => $promoProduct->promo_ends_at?->timestamp,
                'promo_ends_at_label' => $promoProduct->promo_ends_at?->translatedFormat('d F Y'),
                'stock_quantity' => $promoProduct->stock_quantity,
                'image' => $promoProduct->mainImage(),
            ] : null,
            'producer' => $producers->first() ? [
                'slug' => $producers->first()->slug,
                'name' => $producers->first()->name,
                'region' => $producers->first()->region,
                'description' => $producers->first()->description,
            ] : null,
            'testimonials' => $testimonials->take(3)->values()->map(fn (Testimonial $t) => [
                'id' => $t->id,
                'rating' => $t->rating,
                'content' => $t->content,
                'author_name' => $t->author_name,
                'author_role' => $t->author_role,
            ]),
            'showProPrice' => $user?->isApprovedB2B() ?? false,
            'showFeaturedProducts' => Setting::getBool('show_featured_products', true),
            'showProducers' => Setting::getBool('show_producers', true),
            'showTestimonials' => Setting::getBool('show_testimonials', true),
            'showNewsletter' => Setting::getBool('show_newsletter', true),
        ]);
    }
}
