<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class ContentController extends Controller
{
    public function hero()
    {
        $heroSettings = [
            'hero_title'    => SiteSetting::get('hero_title', 'Discover Bangladesh — Hotels, Resorts & Luxury Cruises'),
            'hero_subtitle' => SiteSetting::get('hero_subtitle', "Book top-rated hotels in Cox's Bazar, Sajek, Sylhet and Sundarban luxury ship cruises at guaranteed lowest rates with instant bKash/Nagad confirmation."),
            'slides'        => json_decode(SiteSetting::get('hero_slides', '[]'), true),
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
        if ($request->has('slide_image')) {
            $slides = [];
            $images = $request->slide_image ?? [];
            $badges = $request->slide_badge ?? [];
            foreach ($images as $i => $img) {
                if (!empty($img)) {
                    $slides[] = [
                        'image' => $img,
                        'badge' => $badges[$i] ?? '',
                    ];
                }
            }
            SiteSetting::set('hero_slides', json_encode($slides));
        }

        Cache::flush();

        return back()->with('success', 'Homepage Hero Slider & Banner settings updated successfully in DB!');
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
