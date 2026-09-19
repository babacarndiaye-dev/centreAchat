<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Producer;
use App\Models\Product;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->whereNull('parent_id')->orderBy('position')->take(4)->get();
        $featuredProducts = Product::with(['images', 'producer'])->where('is_active', true)->where('is_featured', true)->take(8)->get();
        $newProducts = Product::with(['images', 'producer'])->where('is_active', true)->where('is_new', true)->take(8)->get();
        $producers = Producer::where('is_active', true)->where('is_featured', true)->take(4)->get();
        $testimonials = Testimonial::where('is_published', true)->take(6)->get();

        $promoProduct = Product::with('images')
            ->where('is_active', true)
            ->whereNotNull('promo_price')
            ->get()
            ->first(fn (Product $product) => $product->isOnPromo());

        return view('home', compact('categories', 'featuredProducts', 'newProducts', 'producers', 'testimonials', 'promoProduct'));
    }
}
