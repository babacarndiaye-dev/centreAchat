<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('title')->paginate(20)->through(fn (Page $page) => [
            'id' => $page->id,
            'title' => $page->title,
            'slug' => $page->slug,
            'is_published' => $page->is_published,
        ]);

        return Inertia::render('Admin/Pages/Index', ['pages' => $pages]);
    }

    public function create()
    {
        return Inertia::render('Admin/Pages/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        Page::create($data);

        return redirect()->route('admin.pages.index')->with('success', 'Page créée.');
    }

    public function edit(Page $page)
    {
        return Inertia::render('Admin/Pages/Form', [
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'content' => $page->content,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
                'is_published' => $page->is_published,
            ],
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $this->validated($request);
        $page->update($data);

        return redirect()->route('admin.pages.index')->with('success', 'Page mise à jour.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return back()->with('success', 'Page supprimée.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'alpha_dash', 'max:255', 'unique:pages,slug,'.$request->route('page')?->id],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }
}
