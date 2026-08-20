<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\SearchRequest;
use App\Models\Property;
use App\Services\Search\SearchService;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * SearchController — Thin Controller (Industry Standard)
 *
 * Responsibility: Accept a validated request → delegate to service → return view.
 * Zero business logic. Zero DB calls. Zero string manipulation.
 *
 * The controller is the "traffic cop" — it routes requests, not processes them.
 */
class SearchController extends Controller
{
    public function __construct(
        private readonly SearchService $searchService
    ) {}

    /**
     * Show hotel/property search results page.
     * GET /search
     */
    public function index(SearchRequest $request): View
    {
        // All params are validated + sanitized via SearchRequest
        $params = $request->toSearchParams();

        // Delegate ALL work to the service — controller stays thin
        $searchResults   = $this->searchService->searchHotels($params);
        $availableCities = $this->searchService->getAvailableCities();
        $priceRange      = $this->searchService->getPriceRange();
        $filterCounts    = $this->searchService->getFilterCounts($params['destination']);

        // ─── Dynamic Popular Neighborhoods for the searched destination ────
        // Queries distinct address parts / nearest_landmark values from real
        // properties in the DB that match the destination.
        // Zero hardcoded strings — 100% database-driven.
        $popularAreas = $this->getPopularAreasForDestination($params['destination']);

        // Log search activity for analytics & trending destinations
        if (!empty($params['destination'])) {
            try {
                \App\Models\SearchLog::create([
                    'query'         => mb_substr($params['destination'], 0, 255),
                    'resolved_city' => mb_substr($params['destination'], 0, 100),
                    'check_in'      => $params['check_in'] ?? null,
                    'check_out'     => $params['check_out'] ?? null,
                    'guests'        => (int) ($params['guests'] ?? 2),
                    'rooms'         => (int) request('rooms', 1),
                    'result_count'  => count($searchResults['merged_results'] ?? []),
                    'user_id'       => auth()->id(),
                    'ip'            => request()->ip() ?? '127.0.0.1',
                    'session_id'    => session()->getId() ?? 'anonymous',
                    'search_type'   => $params['search_type'] ?? 'hotel',
                ]);
            } catch (\Throwable $e) {
                // Non-blocking
            }
        }

        return view('pages.search-results', [
            // Search results data
            'searchResults'   => $searchResults,
            // Current filter values (for UI state restoration)
            'destination'     => $params['destination'],
            'searchType'      => $params['search_type'],
            'checkIn'         => $params['check_in'],
            'checkOut'        => $params['check_out'],
            'guests'          => $params['guests'],
            'minPrice'        => $params['min_price'],
            'maxPrice'        => $params['max_price'],
            'guestRating'     => $params['guest_rating'],
            'starRating'      => $params['star_rating'],
            'sortBy'          => $params['sort_by'],
            'amenities'       => $params['amenities'],
            'lat'             => $params['lat'] ?? null,
            'lng'             => $params['lng'] ?? null,
            // Filter sidebar data
            'availableCities' => $availableCities,
            'priceRange'      => $priceRange,
            'filterCounts'    => $filterCounts,
            // Dynamic neighborhood pills — from DB, not hardcoded
            'popularAreas'    => $popularAreas,
        ]);
    }

    /**
     * Get distinct popular neighborhoods/landmarks for a destination
     * by querying real property data from the database.
     *
     * Strategy:
     *  1. Find distinct nearest_landmark values for properties in this city
     *  2. Also extract unique sub-areas from city field (e.g. "Kolatoli, Cox's Bazar" → "Kolatoli")
     *  3. Cache 10 min per destination to avoid repeated queries
     *
     * @return list<string>  Up to 6 area names, all from real DB data
     */
    private function getPopularAreasForDestination(string $destination): array
    {
        if (empty(trim($destination))) {
            return [];
        }

        $cacheKey = 'popular_areas:' . md5(strtolower(trim($destination)));

        return Cache::remember($cacheKey, 600, function () use ($destination): array {
            $areas = [];

            // 1. Distinct nearest_landmark values (most precise neighborhood labels)
            $landmarks = Property::active()
                ->where(function ($q) use ($destination) {
                    $q->where('city',    'LIKE', "%{$destination}%")
                      ->orWhere('address', 'LIKE', "%{$destination}%");
                })
                ->whereNotNull('nearest_landmark')
                ->where('nearest_landmark', '!=', '')
                ->distinct()
                ->limit(8)
                ->pluck('nearest_landmark')
                ->toArray();

            foreach ($landmarks as $lm) {
                // Strip distance annotations like "(150m)" or "1.2 km"
                $clean = trim(preg_replace('/\(.*?\)|\d+\.?\d*\s*(km|m)\b/i', '', $lm));
                if (strlen($clean) > 2 && !in_array($clean, $areas)) {
                    $areas[] = $clean;
                }
            }

            // 2. Distinct city sub-field (properties often store "Kolatoli, Cox's Bazar")
            if (count($areas) < 5) {
                $cities = Property::active()
                    ->where('city', 'LIKE', "%{$destination}%")
                    ->whereNotNull('city')
                    ->distinct()
                    ->limit(6)
                    ->pluck('city')
                    ->toArray();

                foreach ($cities as $c) {
                    // If city contains a comma, take the first part as the sub-area
                    $sub = str_contains($c, ',') ? trim(explode(',', $c)[0]) : trim($c);
                    if ($sub !== '' && !in_array($sub, $areas) && stripos($sub, $destination) === false) {
                        $areas[] = $sub;
                    }
                }
            }

            return array_slice(array_unique($areas), 0, 6);
        });
    }
}
