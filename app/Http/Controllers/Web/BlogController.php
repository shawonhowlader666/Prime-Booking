<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::where('is_published', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('q')) {
            $s = $request->q;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('excerpt', 'like', "%{$s}%")
                  ->orWhere('content', 'like', "%{$s}%");
            });
        }

        $featuredPosts = BlogPost::where('is_published', true)
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(3)
            ->get();

        $posts = $query->latest('published_at')->paginate(9)->withQueryString();

        $categories = BlogPost::where('is_published', true)
            ->select('category')
            ->distinct()
            ->pluck('category');

        $recentPosts = BlogPost::where('is_published', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('pages.blog.index', compact('posts', 'featuredPosts', 'categories', 'recentPosts'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Increment view count
        $post->increment('views_count');

        // Related posts in same category
        $relatedPosts = BlogPost::where('is_published', true)
            ->where('id', '!=', $post->id)
            ->where('category', $post->category)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('pages.blog.show', compact('post', 'relatedPosts'));
    }
}
