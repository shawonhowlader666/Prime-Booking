<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Models\Amenity;
use App\Models\Property;
use Illuminate\Support\Facades\Cache;

/**
 * FilterAggregator — High-performance aggregation engine for Web & Mobile search filters.
 *
 * Provides a single aggregated payload:
 *  1. Dynamic price range (min/max for price slider)
 *  2. Available property types with active counts
 *  3. Star rating distribution
 *  4. Top amenities list with icons
 *  5. Destination cities list
 *
 * Highly cached (TTL: 300s) to withstand millions of mobile app requests.
 */
class FilterAggregator
{
    private const CACHE_TTL = 300; // 5 minutes

    public function getFilterMetadata(?string $destination = null): array
    {
        $cacheKey = 'search:filters:meta:v1:' . md5((string) $destination);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($destination) {
            $baseQuery = Property::active();

            if (!empty($destination)) {
                $baseQuery->where(function ($q) use ($destination) {
                    $q->where('city', 'LIKE', "%{$destination}%")
                      ->orWhere('name', 'LIKE', "%{$destination}%");
                });
            }

            // 1. Dynamic Price Bounds
            $priceStats = (clone $baseQuery)
                ->selectRaw('MIN(price_per_night) as min_price, MAX(price_per_night) as max_price, AVG(price_per_night) as avg_price')
                ->first();

            $minPrice = floor((float) ($priceStats->min_price ?? 500));
            $maxPrice = ceil((float) ($priceStats->max_price ?? 50000));
            $avgPrice = round((float) ($priceStats->avg_price ?? 5000));

            // 2. Property Type Counts
            $types = (clone $baseQuery)
                ->selectRaw('type, COUNT(*) as count')
                ->whereNotNull('type')
                ->groupBy('type')
                ->get()
                ->map(fn ($r) => [
                    'type'  => $r->type,
                    'label' => ucfirst($r->type),
                    'count' => (int) $r->count,
                ])
                ->values()
                ->toArray();

            // 3. Star Rating Breakdown
            $stars = (clone $baseQuery)
                ->selectRaw('star_rating, COUNT(*) as count')
                ->whereNotNull('star_rating')
                ->groupBy('star_rating')
                ->orderByDesc('star_rating')
                ->get()
                ->map(fn ($r) => [
                    'stars' => (int) $r->star_rating,
                    'count' => (int) $r->count,
                ])
                ->values()
                ->toArray();

            // 4. Popular Amenities
            $amenities = Cache::remember('search:filters:amenities', 600, function () {
                return Amenity::select(['id', 'name', 'icon'])
                    ->limit(15)
                    ->get()
                    ->toArray();
            });

            // 5. Popular Destination Cities
            $cities = Property::active()
                ->selectRaw('city, COUNT(*) as count')
                ->whereNotNull('city')
                ->groupBy('city')
                ->orderByDesc('count')
                ->limit(10)
                ->get()
                ->map(fn ($r) => [
                    'city'  => $r->city,
                    'count' => (int) $r->count,
                ])
                ->values()
                ->toArray();

            return [
                'price_range' => [
                    'min' => $minPrice,
                    'max' => $maxPrice,
                    'avg' => $avgPrice,
                    'currency' => 'BDT',
                ],
                'property_types' => $types,
                'star_ratings'   => $stars,
                'amenities'      => $amenities,
                'cities'         => $cities,
                'sorting_options' => [
                    ['key' => 'recommended', 'label' => 'Recommended / Best Match'],
                    ['key' => 'price_low',   'label' => 'Price (Lowest First)'],
                    ['key' => 'price_high',  'label' => 'Price (Highest First)'],
                    ['key' => 'rating_high', 'label' => 'Rating (Highest First)'],
                ],
            ];
        });
    }
}
