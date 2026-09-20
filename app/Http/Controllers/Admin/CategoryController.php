<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->orderBy('position')->paginate(20);

        return Inertia::render('Admin/Categories/Index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->orderBy('name')->get();

        return Inertia::render('Admin/Categories/Create', compact('parents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie créée.');
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->get();

        return Inertia::render('Admin/Categories/Edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request);

        if ($data['name'] !== $category->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $category->id);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'Catégorie supprimée.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'parent_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'position' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['position'] = $data['position'] ?? 0;

        return $data;
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Category::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
