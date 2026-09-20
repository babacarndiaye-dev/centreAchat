<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(20)->through(fn (Post $post) => [
            'id' => $post->id,
            'title' => $post->title,
            'type' => $post->type,
            'is_published' => $post->is_published,
        ]);

        return Inertia::render('Admin/Posts/Index', ['posts' => $posts]);
    }

    public function create()
    {
        return Inertia::render('Admin/Posts/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('posts', 'public');
        }

        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        Post::create($data);

        return redirect()->route('admin.articles.index')->with('success', 'Article créé.');
    }

    public function edit(Post $post)
    {
        return Inertia::render('Admin/Posts/Form', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'type' => $post->type,
                'excerpt' => $post->excerpt,
                'content' => $post->content,
                'is_published' => $post->is_published,
                'cover_image_url' => $post->cover_image ? asset('fichiers/'.$post->cover_image) : null,
            ],
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $data = $this->validated($request);

        if ($data['title'] !== $post->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $post->id);
        }

        if ($request->hasFile('cover_image')) {
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('posts', 'public');
        }

        if ($data['is_published'] && ! $post->published_at && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post->update($data);

        return redirect()->route('admin.articles.index')->with('success', 'Article mis à jour.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->cover_image) {
            Storage::disk('public')->delete($post->cover_image);
        }

        $post->delete();

        return back()->with('success', 'Article supprimé.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'type' => ['required', 'in:actualite,recette,blog'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        unset($data['cover_image']);

        return $data;
    }

    protected function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (Post::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
