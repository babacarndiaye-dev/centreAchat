<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $user = Auth::user();

        $extra = [];

        if ($slug === 'contact') {
            $extra['contactInfo'] = [
                'address' => Setting::get('address', 'Rond-Point Malicounda, Mbour – Sénégal'),
                'phone' => Setting::get('phone', '+221 XX XXX XX XX'),
                'email' => Setting::get('email', 'contact@centraldachat.sn'),
                'opening_hours' => Setting::get('opening_hours', 'Lun - Sam : 8h - 19h'),
            ];
        }

        if ($slug === 'espace-touristes') {
            $extra['souvenirProducts'] = Product::where('is_active', true)
                ->whereHas('category', fn ($q) => $q->where('slug', 'coffrets-cadeaux'))
                ->with(['images', 'category', 'producer'])->limit(4)->get()
                ->map(fn (Product $p) => $p->toCard($user))->values();
        }

        if ($slug === 'coffrets-cadeaux') {
            $extra['coffrets'] = Product::where('is_active', true)
                ->whereHas('category', fn ($q) => $q->where('slug', 'coffrets-cadeaux'))
                ->with(['images', 'category', 'producer'])->get()
                ->map(fn (Product $p) => $p->toCard($user))->values();
        }

        return Inertia::render('StaticPage', array_merge([
            'page' => [
                'slug' => $page->slug,
                'title' => $page->title,
                'content' => $page->content,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
            ],
        ], $extra));
    }
}
