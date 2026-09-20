<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::where('is_published', true)->latest('published_at');

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        $posts = $query->paginate(9)->withQueryString()->through(fn (Post $post) => [
            'slug' => $post->slug,
            'type' => $post->type,
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'cover_image' => $post->cover_image,
            'published_at' => optional($post->published_at)->format('d/m/Y'),
        ]);

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'currentType' => $request->string('type', '')->toString(),
        ]);
    }

    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)->where('is_published', true)->firstOrFail();

        $related = Post::where('type', $post->type)->where('id', '!=', $post->id)->where('is_published', true)->take(3)->get();

        return Inertia::render('Blog/Show', [
            'post' => [
                'slug' => $post->slug,
                'type' => $post->type,
                'title' => $post->title,
                'content' => $post->content,
                'cover_image' => $post->cover_image,
                'published_at' => optional($post->published_at)->translatedFormat('d F Y'),
            ],
            'related' => $related->map(fn (Post $p) => ['slug' => $p->slug, 'title' => $p->title])->values(),
        ]);
    }
}
