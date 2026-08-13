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
        $lowerQ = strtolower($q);

        $knownCities = [
            ['city' => 'Dhaka', 'country' => 'Bangladesh', 'type' => 'City'],
            ['city' => 'Cox\'s Bazar', 'country' => 'Bangladesh', 'type' => 'City'],
            ['city' => 'Sylhet', 'country' => 'Bangladesh', 'type' => 'City'],
            ['city' => 'Chittagong', 'country' => 'Bangladesh', 'type' => 'City'],
            ['city' => 'Khulna', 'country' => 'Bangladesh', 'type' => 'City'],
            ['city' => 'Sreemangal Upazila', 'country' => 'Bangladesh', 'type' => 'City'],
            ['city' => 'Sajek Valley & Rangamati', 'country' => 'Bangladesh', 'type' => 'City / Region'],
            ['city' => 'Sundarbans & Mongla', 'country' => 'Bangladesh', 'type' => 'Region'],
            ['city' => 'Kuakata Sunset Beach', 'country' => 'Bangladesh', 'type' => 'City'],
            ['city' => 'Bandarban Hill District', 'country' => 'Bangladesh', 'type' => 'Region'],
            ['city' => 'Tanguar Haor & Sunamganj', 'country' => 'Bangladesh', 'type' => 'Region'],
            ['city' => 'Saint Martin\'s Island', 'country' => 'Bangladesh', 'type' => 'Island / Region'],
            ['city' => 'Rajshahi', 'country' => 'Bangladesh', 'type' => 'City'],
            ['city' => 'Barisal', 'country' => 'Bangladesh', 'type' => 'City'],
        ];

        $matchedCities = [];
        if (! empty($lowerQ)) {
            foreach ($knownCities as $kc) {
                if ($lowerQ === 'bangladesh' || $lowerQ === 'bd' || str_contains(strtolower($kc['city']), $lowerQ)) {
                    $matchedCities[] = [
                        'type'     => 'city',
                        'city'     => $kc['city'],
                        'country'  => $kc['country'],
                        'loc_type' => $kc['type'],
                        'title'    => $kc['city'] . ', ' . $kc['country'],
                        'subtitle' => $kc['type'],
                    ];
                }
            }
        }

        // Distinct cities from DB properties
        $dbCities = Property::where('city', 'like', "%{$q}%")
            ->distinct()
            ->pluck('city');

        foreach ($dbCities as $c) {
            $already = false;
            foreach ($matchedCities as $mc) {
                if (strtolower($mc['city']) === strtolower($c)) {
                    $already = true;
                    break;
                }
            }
            if (! $already) {
                $matchedCities[] = [
                    'type'     => 'city',
                    'city'     => $c,
                    'country'  => 'Bangladesh',
                    'loc_type' => 'City',
                    'title'    => $c . ', Bangladesh',
                    'subtitle' => 'City',
                ];
            }
        }

        $destinations = array_slice($matchedCities, 0, 6);

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
                    'type'            => 'property',
                    'id'              => $p->id,
                    'name'            => $p->name,
                    'city'            => $p->city,
                    'address'         => $p->address,
                    'price_per_night' => (float)$p->price_per_night,
                    'primary_image'   => $p->primary_image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=100',
                    'title'           => $p->name,
                    'subtitle'        => $p->city . ' · ' . \App\Services\CurrencyService::format($p->price_per_night) . '/night',
                    'image'           => $p->primary_image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=100',
                    'url'             => route('hotels.show', $p->id),
                ];
            });

        return response()->json([
            'success'      => true,
            'destinations' => $destinations,
            'properties'   => $properties,
            'data'         => [
                'locations'  => $destinations,
                'properties' => $properties,
            ]
        ]);
    }
}
