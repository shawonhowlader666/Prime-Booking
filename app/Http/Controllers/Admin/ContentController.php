<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Models\HeroSlide;
use Illuminate\Support\Facades\Cache;

class ContentController extends Controller
{
    public function hero()
    {
        // Seed sample hero slides if empty
        if (HeroSlide::count() === 0) {
            HeroSlide::create([
                'title'       => 'Cox\'s Bazar Sea Beach Resort',
                'badge_text'  => 'Up to 25% Off Luxury Hotels in Cox\'s Bazar',
                'image_path'  => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200',
                'sort_order'  => 1,
                'status'      => 'active',
            ]);
            HeroSlide::create([
                'title'       => 'Sundarban Houseboat Cruise',
                'badge_text'  => 'Explore Sundarbans Mangrove — MV Zabin Ship',
                'image_path'  => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1200',
                'sort_order'  => 2,
                'status'      => 'active',
            ]);
            HeroSlide::create([
                'title'       => 'Sajek Valley Cloud Cottage',
                'badge_text'  => 'Experience Clouds in Sajek Valley Heights',
                'image_path'  => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200',
                'sort_order'  => 3,
                'status'      => 'active',
            ]);
        }

        $heroTitle = SiteSetting::get('hero_title', 'Discover Bangladesh — Hotels, Resorts & Luxury Cruises');
        $heroSubtitle = SiteSetting::get('hero_subtitle', "Book top-rated hotels in Cox's Bazar, Sajek, Sylhet and Sundarban luxury ship cruises at guaranteed lowest rates with instant bKash/Nagad confirmation.");
        $slides = HeroSlide::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.content.hero', compact('heroTitle', 'heroSubtitle', 'slides'));
    }

    public function updateHero(Request $request)
    {
        if ($request->filled('hero_title')) {
            SiteSetting::set('hero_title', $request->hero_title);
        }
        if ($request->filled('hero_subtitle')) {
            SiteSetting::set('hero_subtitle', $request->hero_subtitle);
        }

        Cache::flush();
        return back()->with('success', 'Homepage Hero main heading & subtitle updated successfully!');
    }

    public function storeSlide(Request $request)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'badge_text' => 'nullable|string|max:255',
            'image_url'  => 'nullable|string|max:1000',
            'slide_file' => 'nullable|image|max:5120',
            'sort_order' => 'nullable|integer',
        ]);

        $imagePath = $request->image_url ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200';

        if ($request->hasFile('slide_file') && $request->file('slide_file')->isValid()) {
            $path = $request->file('slide_file')->store('uploads/hero', 'public');
            $imagePath = asset('storage/' . $path);
        }

        HeroSlide::create([
            'title'      => $validated['title'],
            'badge_text' => $validated['badge_text'] ?? '',
            'image_path' => $imagePath,
            'sort_order' => $validated['sort_order'] ?? (HeroSlide::max('sort_order') + 1),
            'status'     => 'active',
        ]);

        Cache::flush();
        return back()->with('success', 'New Banner Slide created successfully!');
    }

    public function updateSlide(Request $request, $id)
    {
        $slide = HeroSlide::findOrFail($id);

        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'badge_text' => 'nullable|string|max:255',
            'image_url'  => 'nullable|string|max:1000',
            'slide_file' => 'nullable|image|max:5120',
            'sort_order' => 'nullable|integer',
            'status'     => 'required|in:active,inactive',
        ]);

        $imagePath = $request->image_url ?: $slide->image_path;

        if ($request->hasFile('slide_file') && $request->file('slide_file')->isValid()) {
            $path = $request->file('slide_file')->store('uploads/hero', 'public');
            $imagePath = asset('storage/' . $path);
        }

        $slide->update([
            'title'      => $validated['title'],
            'badge_text' => $validated['badge_text'] ?? '',
            'image_path' => $imagePath,
            'sort_order' => $validated['sort_order'] ?? $slide->sort_order,
            'status'     => $validated['status'],
        ]);

        Cache::flush();
        return back()->with('success', "Banner Slide #{$slide->id} updated successfully!");
    }

    public function toggleSlide($id)
    {
        $slide = HeroSlide::findOrFail($id);
        $slide->status = ($slide->status === 'active') ? 'inactive' : 'active';
        $slide->save();

        Cache::flush();
        return back()->with('success', "Slide #{$slide->id} status changed to {$slide->status}!");
    }

    public function destroySlide($id)
    {
        $slide = HeroSlide::findOrFail($id);
        $slide->delete();

        Cache::flush();
        return back()->with('success', 'Banner Slide deleted successfully!');
    }

    public function destinations()
    {
        $destinations = \App\Models\FeaturedDestination::all();
        return view('admin.content.destinations', compact('destinations'));
    }

    public function updateDestinations(Request $request)
    {
        $dest = $request->input('destinations', []);
        foreach ($dest as $id => $data) {
            $item = \App\Models\FeaturedDestination::find($id);
            if ($item) {
                $item->update([
                    'name'        => $data['name'] ?? $item->name,
                    'is_featured' => isset($data['is_featured']),
                ]);
            }
        }
        Cache::flush();
        return back()->with('success', 'Featured Tourist Destinations updated successfully in DB!');
    }
}
