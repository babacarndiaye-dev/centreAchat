<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProducerController extends Controller
{
    public function index()
    {
        $producers = Producer::orderBy('name')->paginate(20);

        return Inertia::render('Admin/Producers/Index', compact('producers'));
    }

    public function create()
    {
        return Inertia::render('Admin/Producers/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('producers', 'public');
        }

        Producer::create($data);

        return redirect()->route('admin.producteurs.index')->with('success', 'Producteur créé.');
    }

    public function edit(Producer $producer)
    {
        return Inertia::render('Admin/Producers/Edit', [
            'producer' => [
                ...$producer->only(['id', 'name', 'region', 'description', 'is_featured', 'is_active']),
                'photo_url' => $producer->photo ? asset('fichiers/'.$producer->photo) : null,
            ],
        ]);
    }

    public function update(Request $request, Producer $producer): RedirectResponse
    {
        $data = $this->validated($request);

        if ($data['name'] !== $producer->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $producer->id);
        }

        if ($request->hasFile('photo')) {
            if ($producer->photo) {
                Storage::disk('public')->delete($producer->photo);
            }
            $data['photo'] = $request->file('photo')->store('producers', 'public');
        }

        $producer->update($data);

        return redirect()->route('admin.producteurs.index')->with('success', 'Producteur mis à jour.');
    }

    public function destroy(Producer $producer): RedirectResponse
    {
        $producer->delete();

        return back()->with('success', 'Producteur supprimé.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');
        unset($data['photo']);

        return $data;
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Producer::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
