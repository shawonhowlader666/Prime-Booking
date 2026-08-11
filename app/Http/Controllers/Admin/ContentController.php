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
            'site_name'    => SiteSetting::get('site_name', 'PRIME BOOKING'),
            'site_tagline' => SiteSetting::get('site_tagline', 'Bangladesh\'s #1 Hotel & Flight Platform'),
            'hero_banner'  => SiteSetting::get('hero_banner_title', 'Find Your Next Stay or Flight'),
        ];
        return view('admin.content.hero', compact('heroSettings'));
    }

    public function updateHero(Request $request)
    {
        $validated = $request->validate([
            'site_name'    => 'nullable|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'hero_banner'  => 'nullable|string|max:255',
        ]);

        if (!empty($validated['site_name'])) {
            SiteSetting::set('site_name', $validated['site_name']);
        }
        if (!empty($validated['site_tagline'])) {
            SiteSetting::set('site_tagline', $validated['site_tagline']);
        }
        if (!empty($validated['hero_banner'])) {
            SiteSetting::set('hero_banner_title', $validated['hero_banner']);
        }

        Cache::flush();

        return back()->with('success', 'Homepage Hero Slider & Banner text updated successfully in DB!');
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

