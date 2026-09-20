<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class ProductReviewController extends Controller
{
    public function index()
    {
        $reviews = ProductReview::with(['product', 'user'])->latest()->paginate(20)->through(fn (ProductReview $review) => [
            'id' => $review->id,
            'product_name' => $review->product->name,
            'user_name' => $review->user->name,
            'rating' => $review->rating,
            'comment' => $review->comment,
            'is_approved' => $review->is_approved,
        ]);

        return Inertia::render('Admin/ProductReviews/Index', ['reviews' => $reviews]);
    }

    public function toggle(ProductReview $avisProduit): RedirectResponse
    {
        $avisProduit->update(['is_approved' => ! $avisProduit->is_approved]);

        return back()->with('success', $avisProduit->is_approved ? 'Avis republié.' : 'Avis masqué.');
    }

    public function destroy(ProductReview $avisProduit): RedirectResponse
    {
        $avisProduit->delete();

        return back()->with('success', 'Avis supprimé.');
    }
}
