<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::where('is_published', true)->latest('published_at');

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        $posts = $query->paginate(9)->withQueryString();

        return view('blog.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)->where('is_published', true)->firstOrFail();

        $related = Post::where('type', $post->type)->where('id', '!=', $post->id)->where('is_published', true)->take(3)->get();

        return view('blog.show', compact('post', 'related'));
    }
}
