<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    public function hero()
    {
        $defaultSlides = [
            [
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200',
                'badge' => 'Up to 25% Off Luxury Hotels in Cox\'s Bazar',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1200',
                'badge' => 'Explore Sundarbans Mangrove — MV Zabin Ship',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200',
                'badge' => 'Experience Clouds in Sajek Valley Heights',
            ],
        ];

        $rawSlides = SiteSetting::get('hero_slides');
        $slides = !empty($rawSlides) ? json_decode($rawSlides, true) : $defaultSlides;
        if (empty($slides)) {
            $slides = $defaultSlides;
        }

        $heroSettings = [
            'hero_title'    => SiteSetting::get('hero_title', 'Discover Bangladesh — Hotels, Resorts & Luxury Cruises'),
            'hero_subtitle' => SiteSetting::get('hero_subtitle', "Book top-rated hotels in Cox's Bazar, Sajek, Sylhet and Sundarban luxury ship cruises at guaranteed lowest rates with instant bKash/Nagad confirmation."),
            'slides'        => $slides,
        ];
        return view('admin.content.hero', compact('heroSettings'));
    }

    public function updateHero(Request $request)
    {
        if ($request->filled('hero_title')) {
            SiteSetting::set('hero_title', $request->hero_title);
        }
        if ($request->filled('hero_subtitle')) {
            SiteSetting::set('hero_subtitle', $request->hero_subtitle);
        }

        $images = $request->slide_image ?? [];
        $badges = $request->slide_badge ?? [];
        $files  = $request->file('slide_file') ?? [];

        $slides = [];
        foreach ($images as $i => $img) {
            $finalUrl = $img;

            if (isset($files[$i]) && $files[$i]->isValid()) {
                $path = $files[$i]->store('uploads/hero', 'public');
                $finalUrl = asset('storage/' . $path);
            }

            if (!empty($finalUrl)) {
                $slides[] = [
                    'image' => $finalUrl,
                    'badge' => $badges[$i] ?? '',
                ];
            }
        }

        SiteSetting::set('hero_slides', json_encode($slides));
        Cache::flush();

        return back()->with('success', 'Homepage Hero Slider & Banners updated successfully with image upload support!');
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
