<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::latest()->paginate(20)->through(fn (Testimonial $testimonial) => [
            'id' => $testimonial->id,
            'author_name' => $testimonial->author_name,
            'author_role' => $testimonial->author_role,
            'rating' => $testimonial->rating,
            'is_published' => $testimonial->is_published,
        ]);

        return Inertia::render('Admin/Testimonials/Index', ['testimonials' => $testimonials]);
    }

    public function create()
    {
        return Inertia::render('Admin/Testimonials/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        Testimonial::create($this->validated($request));

        return redirect()->route('admin.avis.index')->with('success', 'Avis créé.');
    }

    public function edit(Testimonial $testimonial)
    {
        return Inertia::render('Admin/Testimonials/Form', [
            'testimonial' => [
                'id' => $testimonial->id,
                'author_name' => $testimonial->author_name,
                'author_role' => $testimonial->author_role,
                'content' => $testimonial->content,
                'rating' => $testimonial->rating,
                'is_published' => $testimonial->is_published,
            ],
        ]);
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update($this->validated($request));

        return redirect()->route('admin.avis.index')->with('success', 'Avis mis à jour.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return back()->with('success', 'Avis supprimé.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'author_name' => ['required', 'string', 'max:255'],
            'author_role' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }
}
