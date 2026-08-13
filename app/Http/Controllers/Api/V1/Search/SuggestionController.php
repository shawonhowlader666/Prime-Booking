<?php

namespace App\Http\Controllers\Api\V1\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Location;
use App\Traits\ApiResponseTrait;
use Symfony\Component\HttpFoundation\Response;

class SuggestionController extends Controller
{
    use ApiResponseTrait;

    /**
     * Real-time Autocomplete Suggestions API (Agoda Exact Parity)
     * GET /api/v1/search/suggestions?q=cox&search_type=hotel
     */
    public function suggestions(Request $request)
    {
        $query = trim($request->query('q', ''));
        $searchType = $request->query('search_type', 'hotel');

        if (empty($query)) {
            // 100% Bangladesh Local Destinations & Tourist Hubs
            $bdDestinations = [
                ['city' => 'Cox\'s Bazar', 'count' => 118, 'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=120&q=80'],
                ['city' => 'Chittagong', 'count' => 59, 'image' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=120&q=80'],
                ['city' => 'Sylhet', 'count' => 95, 'image' => 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?auto=format&fit=crop&w=120&q=80'],
                ['city' => 'Dhaka', 'count' => 538, 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80'],
                ['city' => 'Sreemangal', 'count' => 25, 'image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=120&q=80'],
                ['city' => 'Rajshahi', 'count' => 11, 'image' => 'https://images.unsplash.com/photo-1587061949409-02df41d5e562?auto=format&fit=crop&w=120&q=80'],
            ];

            $regionalDestinations = [
                ['city' => 'Sajek Valley & Rangamati', 'count' => 45, 'tags' => 'cloud hills, eco resorts'],
                ['city' => 'Sundarbans & Mongla', 'count' => 38, 'tags' => 'forest cruise, ships'],
                ['city' => 'Tanguar Haor & Sunamganj', 'count' => 28, 'tags' => 'houseboats, haor tour'],
                ['city' => 'Saint Martin\'s Island', 'count' => 32, 'tags' => 'coral island, sea view'],
                ['city' => 'Kuakata Sea Beach', 'count' => 22, 'tags' => 'sunrise, sunset beach'],
            ];

            return $this->successResponse([
                'bd_destinations' => $bdDestinations,
                'regional_destinations' => $regionalDestinations,
            ], '100% Bangladesh destinations retrieved.');
        }

        // Live typing autocomplete search query (Agoda Exact Parity)
        $lowerQ = strtolower($query);
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
        foreach ($knownCities as $kc) {
            if ($lowerQ === 'bangladesh' || $lowerQ === 'bd' || str_contains(strtolower($kc['city']), $lowerQ)) {
                $matchedCities[] = $kc;
            }
        }

        // Also query distinct cities from DB properties
        $dbCities = Property::where('city', 'like', "%{$query}%")
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
                $matchedCities[] = ['city' => $c, 'country' => 'Bangladesh', 'type' => 'City'];
            }
        }

        $locations = array_slice($matchedCities, 0, 6);

        $propertyQuery = Property::with('location')
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%")
                  ->orWhere('city', 'like', "%{$query}%");
            });

        if ($searchType === 'houseboat') {
            $propertyQuery->where(function($q) {
                $q->where('type', 'like', '%Ship%')
                  ->orWhere('type', 'like', '%Houseboat%')
                  ->orWhere('type', 'like', '%Resort%')
                  ->orWhere('name', 'like', '%Sundarban%')
                  ->orWhere('name', 'like', '%Haor%');
            });
        } elseif ($searchType === 'homestay') {
            $propertyQuery->where(function($q) {
                $q->where('type', 'like', '%Homestay%')
                  ->orWhere('type', 'like', '%Apartment%')
                  ->orWhere('type', 'like', '%Villa%');
            });
        }

        $properties = $propertyQuery->take(5)->get();

        return $this->successResponse([
            'query'      => $query,
            'locations'  => $locations,
            'properties' => $properties,
        ], 'Real-time Agoda-style suggestions retrieved.');
    }
}
