<?php

namespace App\Http\Controllers;

use App\Models\Producer;

class ProducerController extends Controller
{
    public function index()
    {
        $producers = Producer::withCount('products')->where('is_active', true)->orderBy('name')->paginate(12);

        return view('producers.index', compact('producers'));
    }

    public function show(string $slug)
    {
        $producer = Producer::with('products.images')->where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('producers.show', compact('producer'));
    }
}
