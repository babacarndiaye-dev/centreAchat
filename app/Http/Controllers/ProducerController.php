<?php

namespace App\Http\Controllers;

use App\Models\Producer;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProducerController extends Controller
{
    public function index()
    {
        $producers = Producer::withCount('products')->where('is_active', true)->orderBy('name')->paginate(12)
            ->through(fn (Producer $p) => [
                'slug' => $p->slug,
                'name' => $p->name,
                'region' => $p->region,
                'description' => $p->description,
                'photo' => $p->photo,
                'products_count' => $p->products_count,
            ]);

        return Inertia::render('Producers/Index', ['producers' => $producers]);
    }

    public function show(string $slug)
    {
        $user = Auth::user();
        $producer = Producer::with(['products' => fn ($q) => $q->where('is_active', true), 'products.images', 'products.category', 'products.producer'])
            ->where('slug', $slug)->where('is_active', true)->firstOrFail();

        return Inertia::render('Producers/Show', [
            'producer' => [
                'name' => $producer->name,
                'region' => $producer->region,
                'description' => $producer->description,
                'photo' => $producer->photo,
            ],
            'products' => $producer->products->map(fn ($p) => $p->toCard($user))->values(),
        ]);
    }
}
