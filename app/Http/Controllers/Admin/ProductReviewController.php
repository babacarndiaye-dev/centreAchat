<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;

class ProductReviewController extends Controller
{
    public function index()
    {
        $reviews = ProductReview::with(['product', 'user'])->latest()->paginate(20);

        return view('admin.product-reviews.index', compact('reviews'));
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
