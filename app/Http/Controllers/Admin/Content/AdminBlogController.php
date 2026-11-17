<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminBlogController extends Controller
{
    protected function ensureDefaultsExist(): void
    {
        if (BlogPost::count() > 0) {
            return;
        }

        $defaults = [
            [
                'title'          => 'Top 10 Luxury Resorts in Cox\'s Bazar for 2026',
                'slug'           => 'top-10-luxury-resorts-in-coxs-bazar-2026',
                'category'       => 'Resort Guides',
                'excerpt'        => 'Explore the most serene oceanfront resorts, infinity pools, and five-star hospitality on the world\'s longest natural sea beach.',
                'content'        => '<p>Cox’s Bazar is globally recognized for having the longest natural unbroken sandy beach in the world. Whether you are traveling for a romantic honeymoon, a family vacation, or a high-level corporate retreat, choosing the right luxury resort makes all the difference.</p><h3>1. Ocean Paradise Hotel & Resort</h3><p>Known for panoramic bay views and multi-cuisine rooftop dining, Ocean Paradise continues to be an iconic landmark in Kolatoli.</p><h3>2. Sayeman Beach Resort</h3><p>Direct beachfront infinity pool overlooking the surf, private cabanas, and unmatched sunset cocktails.</p><p>Book early during the winter peak season to secure special PrimeVIP rates.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1200&q=80',
                'author_name'    => 'Prime Editorial Desk',
                'is_published'   => true,
                'is_featured'    => true,
                'published_at'   => now()->subDays(2),
                'views_count'    => 1420,
            ],
            [
                'title'          => 'The Ultimate Travel Guide to Sylhet Tea Gardens & Sreemangal',
                'slug'           => 'ultimate-travel-guide-to-sylhet-tea-gardens-sreemangal',
                'category'       => 'Travel Guide',
                'excerpt'        => 'From walking through verdant rolling hills to tasting authentic seven-layer tea, here is everything you need to know about visiting the tea capital.',
                'content'        => '<p>Known as the Tea Capital of Bangladesh, Sreemangal offers emerald landscapes, pristine rain forests like Lawachara, and cozy boutique eco-resorts nestled among rubber and pineapple groves.</p><h3>Best Time to Visit</h3><p>Between October and March, the morning mist covering the tea estates creates a postcard-perfect view.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80',
                'author_name'    => 'Farhana Rahman',
                'is_published'   => true,
                'is_featured'    => false,
                'published_at'   => now()->subDays(5),
                'views_count'    => 980,
            ],
            [
                'title'          => 'How to Maximize Your PrimeVIP Cashback & Reward Points',
                'slug'           => 'how-to-maximize-your-primevip-cashback-reward-points',
                'category'       => 'Tips & Deals',
                'excerpt'        => 'Learn insider tips on stacking coupons, booking advance deals, and earning double miles on domestic and international properties.',
                'content'        => '<p>Traveling smartly means making every single Taka work for you. With the Prime Booking VIP Club, every night stayed accumulates tier credits that unlock room upgrades and instant cashback.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80',
                'author_name'    => 'Prime Loyalty Team',
                'is_published'   => true,
                'is_featured'    => false,
                'published_at'   => now()->subDays(8),
                'views_count'    => 650,
            ]
        ];

        foreach ($defaults as $item) {
            BlogPost::create($item);
        }
    }

    public function index(Request $request)
    {
        $this->ensureDefaultsExist();

        $query = BlogPost::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('excerpt', 'like', "%{$s}%")
                  ->orWhere('category', 'like', "%{$s}%");
            });
        }

        $posts = $query->latest('id')->paginate(per_page(12))->withQueryString();

        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'required|string|max:100',
            'excerpt'          => 'nullable|string|max:1000',
            'content'          => 'required|string',
            'featured_image'   => 'nullable|string|max:500',
            'image_file'       => 'nullable|image|max:3072',
            'author_name'      => 'nullable|string|max:150',
            'is_published'     => 'boolean',
            'is_featured'      => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $validated['author_id'] = auth()->id();
        $validated['author_name'] = $request->author_name ?: (auth()->user()->name ?? 'Prime Editorial');
        $validated['slug'] = BlogPost::generateSlug($request->title);
        $validated['is_published'] = $request->has('is_published');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
            $path = $request->file('image_file')->store('uploads/blog', 'public');
            $validated['featured_image'] = asset('storage/' . $path);
        }

        BlogPost::create($validated);

        return redirect()->route('admin.blog.index')->with('success', 'Blog article published successfully!');
    }

    public function edit($id)
    {
        $post = BlogPost::findOrFail($id);
        return view('admin.blog.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'required|string|max:100',
            'excerpt'          => 'nullable|string|max:1000',
            'content'          => 'required|string',
            'featured_image'   => 'nullable|string|max:500',
            'image_file'       => 'nullable|image|max:3072',
            'author_name'      => 'nullable|string|max:150',
            'is_published'     => 'boolean',
            'is_featured'      => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $validated['is_published'] = $request->has('is_published');
        $validated['is_featured'] = $request->has('is_featured');

        if ($validated['is_published'] && ! $post->published_at) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
            $path = $request->file('image_file')->store('uploads/blog', 'public');
            $validated['featured_image'] = asset('storage/' . $path);
        }

        $post->update($validated);

        return redirect()->route('admin.blog.index')->with('success', 'Blog article updated successfully!');
    }

    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);
        $post->delete();

        return redirect()->route('admin.blog.index')->with('success', 'Article deleted.');
    }

    public function togglePublish($id)
    {
        $post = BlogPost::findOrFail($id);
        $post->is_published = ! $post->is_published;
        if ($post->is_published && ! $post->published_at) {
            $post->published_at = now();
        }
        $post->save();

        return back()->with('success', 'Article status updated.');
    }

    public function toggleFeature($id)
    {
        $post = BlogPost::findOrFail($id);
        $post->is_featured = ! $post->is_featured;
        $post->save();

        return back()->with('success', 'Featured status updated.');
    }
}
