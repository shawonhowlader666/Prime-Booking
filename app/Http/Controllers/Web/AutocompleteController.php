<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\FeaturedDestination;
use Illuminate\Http\Request;

class AutocompleteController extends Controller
{
    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['success' => true, 'destinations' => [], 'properties' => []]);
        }

        $destinations = FeaturedDestination::where('is_active', true)
            ->where(function($query) use ($q) {
                $query->where('city', 'like', "%{$q}%")
                      ->orWhere('country', 'like', "%{$q}%");
            })
            ->take(4)
            ->get()
            ->map(function($d) {
                return [
                    'type'     => 'city',
                    'title'    => $d->city . ', ' . ($d->country ?: 'Bangladesh'),
                    'subtitle' => $d->property_count . ' verified properties',
                    'image'    => $d->image_url ?: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=100',
                    'url'      => route('search.index', ['destination' => $d->city]),
                ];
            });

        $properties = Property::active()
            ->where(function($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('city', 'like', "%{$q}%")
                      ->orWhere('address', 'like', "%{$q}%");
            })
            ->take(5)
            ->get()
            ->map(function($p) {
                return [
                    'type'     => 'property',
                    'title'    => $p->name,
                    'subtitle' => $p->city . ' · ' . \App\Services\CurrencyService::format($p->price_per_night) . '/night',
                    'image'    => $p->primary_image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=100',
                    'url'      => route('hotels.show', $p->id),
                ];
            });

        return response()->json([
            'success'      => true,
            'destinations' => $destinations,
            'properties'   => $properties,
        ]);
    }
}
