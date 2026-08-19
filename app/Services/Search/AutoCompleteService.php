<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Models\Location;
use App\Models\Property;
use App\Models\SearchLog;
use App\Jobs\LogSearchJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

/**
 * AutoCompleteService — World-Class Search Autocomplete Engine
 *
 * Architecture:
 *  1. Priority-scored suggestions (exact > starts-with > contains + trending bonus)
 *  2. Two-tier cache: L1=60s (hot queries), L2=300s (warm city data)
 *  3. Trending data from search_logs (last 7 days, top searches)
 *  4. Fuzzy matching for typo tolerance (Levenshtein ≤ 2)
 *  5. Personalization for authenticated users (last 3 searches)
 *  6. Async search logging via LogSearchJob (zero latency)
 *  7. CityInsightService integration for contextual weather tips
 *
 * @see App\Jobs\LogSearchJob
 * @see App\Services\Search\CityInsightService
 */
class AutoCompleteService
{
    private const TTL_HOT      = 60;    // 1 min  — trending changes fast
    private const TTL_WARM     = 300;   // 5 min  — static city data
    private const TTL_TRENDING = 600;   // 10 min — aggregated counts

    public function __construct(
        private readonly LocationNormalizerService $normalizer = new LocationNormalizerService()
    ) {}

    // ─── MAIN ENTRY POINT ─────────────────────────────────────────────────

    /**
     * Get full autocomplete payload for a query string.
     * Called by AutocompleteController on every keystroke (debounced 180ms in JS).
     */
    public function getSuggestions(string $query, string $searchType = 'hotel', int $limit = 8): array
    {
        $query = trim($query);

        if (strlen($query) < 1) {
            return $this->getDefaultPayload($searchType);
        }

        // Canonical normalization (e.g. 'কক্সবাজার' -> "Cox's Bazar", 'coxsbazar' -> "Cox's Bazar")
        $canonical = $this->normalizer->normalize($query);
        $searchTarget = $canonical ?: $query;

        $cacheKey = "autocomplete:v3:{$searchType}:" . md5(strtolower($searchTarget) . ':' . $limit);

        return Cache::remember($cacheKey, self::TTL_HOT, function () use ($query, $searchTarget, $searchType, $limit): array {
            $locations  = $this->scoreAndRankLocations($searchTarget, $limit);
            $properties = $this->fetchProperties($searchTarget, $searchType, 5);
            $insight    = $this->getCityInsight($searchTarget, $locations);

            return [
                'locations'        => $locations,
                'properties'       => $properties,
                'insight'          => $insight,
                'canonical_match'  => $searchTarget !== $query ? $searchTarget : null,
                'trending'         => [],   // Not shown in typing state
            ];
        });
    }

    /**
     * Default payload when search box is clicked but nothing typed yet.
     * Shows: trending cities + personalized recent + static Bangladesh grid.
     */
    public function getDefaultPayload(string $searchType = 'hotel'): array
    {
        $cacheKey = "autocomplete:v3:default:{$searchType}";

        $trending = $this->getTrending(6);
        $personal = $this->getPersonalizedSuggestions(3);

        // Default BD cities (cache 5 min — very stable)
        $bdCities = Cache::remember($cacheKey . ':bd', self::TTL_WARM, function (): array {
            return Property::active()
                ->select('city')
                ->selectRaw('COUNT(*) as property_count')
                ->selectRaw('MIN(price_per_night) as min_price')
                ->selectRaw('MIN(primary_image) as image_url')
                ->whereNotNull('city')
                ->groupBy('city')
                ->orderByDesc('property_count')
                ->limit(6)
                ->get()
                ->map(fn ($r) => [
                    'city'           => $r->city,
                    'country'        => 'Bangladesh',
                    'property_count' => (int) $r->property_count,
                    'min_price'      => (float) $r->min_price,
                    'image_url'      => $r->image_url
                        ? (str_starts_with($r->image_url, 'http')
                            ? $r->image_url
                            : asset('storage/' . ltrim($r->image_url, '/')))
                        : null,
                ])
                ->toArray();
        });

        $international = [
            ['city' => 'Singapore',    'country' => 'Singapore', 'property_count' => 1326, 'tags' => 'shopping, restaurants'],
            ['city' => 'Bangkok',      'country' => 'Thailand',  'property_count' => 12048,'tags' => 'shopping, restaurants'],
            ['city' => 'Kuala Lumpur', 'country' => 'Malaysia',  'property_count' => 19902,'tags' => 'shopping, restaurants'],
        ];

        return [
            'bd_destinations'        => $bdCities,
            'international'          => $international,
            'trending'               => $trending,
            'personalized'           => $personal,
            'locations'              => [],
            'properties'             => [],
        ];
    }

    // ─── TRENDING ─────────────────────────────────────────────────────────

    /**
     * Top N trending search queries from the last 7 days.
     * Only counts searches that had results (result_count > 0).
     */
    public function getTrending(int $limit = 6): array
    {
        return Cache::remember("search:trending:v2:{$limit}", self::TTL_TRENDING, function () use ($limit): array {
            return SearchLog::withResults()
                ->recent(7)
                ->selectRaw('resolved_city as city, COUNT(*) as search_count')
                ->whereNotNull('resolved_city')
                ->groupBy('resolved_city')
                ->orderByDesc('search_count')
                ->limit($limit)
                ->get()
                ->map(fn ($r) => [
                    'city'         => $r->city,
                    'search_count' => (int) $r->search_count,
                ])
                ->toArray();
        });
    }

    // ─── PERSONALIZATION ──────────────────────────────────────────────────

    /**
     * Last N searches for the authenticated user.
     * Returns [] for guests.
     */
    public function getPersonalizedSuggestions(int $limit = 3): array
    {
        if (!Auth::check()) {
            return [];
        }

        $userId = Auth::id();

        return Cache::remember("search:personal:{$userId}", 120, function () use ($userId, $limit): array {
            return SearchLog::where('user_id', $userId)
                ->withResults()
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get()
                ->map(fn ($log) => [
                    'query'      => $log->query,
                    'city'       => $log->resolved_city,
                    'check_in'   => $log->check_in?->format('j M Y'),
                    'check_out'  => $log->check_out?->format('j M Y'),
                    'guests'     => $log->guests,
                ])
                ->toArray();
        });
    }

    // ─── ASYNC LOGGING ────────────────────────────────────────────────────

    /**
     * Dispatch search log to queue. Zero latency — fire and forget.
     */
    public function logSearch(
        string  $query,
        ?string $resolvedCity,
        array   $params = [],
        int     $resultCount = 0
    ): void {
        try {
            LogSearchJob::dispatch([
                'query'         => $query,
                'resolved_city' => $resolvedCity,
                'check_in'      => $params['check_in']    ?? null,
                'check_out'     => $params['check_out']   ?? null,
                'guests'        => $params['guests']      ?? 1,
                'rooms'         => $params['rooms']       ?? 1,
                'result_count'  => $resultCount,
                'search_type'   => $params['search_type'] ?? 'hotel',
                'user_id'       => Auth::id(),
                'ip'            => request()->ip(),
                'session_id'    => session()->getId(),
            ]);
        } catch (\Throwable) {
            // Silent fail — logging must never crash the API
        }
    }

    // ─── PRIVATE HELPERS ──────────────────────────────────────────────────

    /**
     * Priority-scored location suggestions:
     *  +100 exact match | +80 starts-with | +60 contains | +30 trending bonus
     */
    private function scoreAndRankLocations(string $query, int $limit): array
    {
        $lq = strtolower($query);

        // 1. DB locations (admin-managed hierarchy)
        $locationRows = Location::where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('city', 'LIKE', "%{$query}%");
            })
            ->orderByDesc('is_popular')
            ->limit(8)
            ->get();

        // 2. Distinct cities from live properties (vendor data)
        $propertyCities = Property::active()
            ->where(function ($q) use ($query) {
                $q->where('city', 'LIKE', "%{$query}%")
                  ->orWhere('address', 'LIKE', "%{$query}%");
            })
            ->distinct()
            ->limit(6)
            ->pluck('city')
            ->filter()
            ->toArray();

        // 3. Trending bonus cities
        $trendingCities = array_column($this->getTrending(10), 'city');
        $trendingIndex  = array_flip(array_map('strtolower', $trendingCities));

        $results   = [];
        $seenCities = [];

        // Process DB locations
        foreach ($locationRows as $loc) {
            $cityName = $loc->name;
            $key      = strtolower($cityName);
            if (isset($seenCities[$key])) continue;
            $seenCities[$key] = true;

            $score = $this->calcScore($lq, strtolower($cityName));
            if (isset($trendingIndex[$key])) $score += 30;

            $results[] = [
                'city'           => $cityName,
                'country'        => $loc->country ?? 'Bangladesh',
                'type'           => 'City',   // Fixed: was accidentally using $loc->city as type
                'property_count' => null,
                'lat'            => $loc->latitude,
                'lng'            => $loc->longitude,
                '_score'         => $score,
            ];
        }

        // Process property cities
        foreach ($propertyCities as $city) {
            $key = strtolower($city);
            if (isset($seenCities[$key])) continue;
            $seenCities[$key] = true;

            $score = $this->calcScore($lq, $key);
            if (isset($trendingIndex[$key])) $score += 30;

            $results[] = [
                'city'           => $city,
                'country'        => 'Bangladesh',
                'type'           => 'City / Area',
                'property_count' => null,
                'lat'            => null,
                'lng'            => null,
                '_score'         => $score,
            ];
        }

        // Sort by score descending, remove internal score key
        usort($results, fn ($a, $b) => $b['_score'] <=> $a['_score']);

        return array_map(function ($r) {
            unset($r['_score']);
            return $r;
        }, array_slice($results, 0, $limit));
    }

    /** Score a single candidate city name against the query. */
    private function calcScore(string $query, string $candidate): int
    {
        if ($candidate === $query)               return 100; // Exact
        if (str_starts_with($candidate, $query)) return 80;  // Starts-with
        if (str_contains($candidate, $query))    return 60;  // Contains

        // Fuzzy: Levenshtein ≤ 2 (typo tolerance) for queries ≥ 4 chars
        if (strlen($query) >= 4 && levenshtein($query, $candidate) <= 2) return 40;

        return 0;
    }

    /** Fetch matching properties with proper image URLs and type labels. */
    private function fetchProperties(string $query, string $searchType, int $limit): array
    {
        $builder = Property::active()
            ->where(function ($q) use ($query) {
                $q->where('name',              'LIKE', "%{$query}%")
                  ->orWhere('city',            'LIKE', "%{$query}%")
                  ->orWhere('address',         'LIKE', "%{$query}%")
                  ->orWhere('nearest_landmark','LIKE', "%{$query}%");
            });

        // Search type filter
        if ($searchType === 'houseboat') {
            $builder->where(function ($q) {
                $q->where('type', 'like', '%Ship%')
                  ->orWhere('type', 'like', '%Houseboat%')
                  ->orWhere('name', 'like', '%Sundarban%')
                  ->orWhere('name', 'like', '%Haor%');
            });
        } elseif ($searchType === 'homestay') {
            $builder->where(function ($q) {
                $q->where('type', 'like', '%Homestay%')
                  ->orWhere('type', 'like', '%Apartment%')
                  ->orWhere('type', 'like', '%Villa%');
            });
        }

        return $builder
            ->select(['id', 'name', 'city', 'address', 'price_per_night', 'primary_image', 'rating_score', 'type'])
            ->limit($limit)
            ->get()
            ->map(function ($p) {
                $img = null;
            if ($p->primary_image) {
                $raw = $p->primary_image;
                if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
                    $img = $raw;  // Already absolute URL — use as-is
                } else {
                    $img = asset('storage/' . ltrim($raw, '/'));  // Relative path → storage URL
                }
            }

                // Agoda-style Smart Urgency / Rating Badge
                $ratingScore = (float) $p->rating_score;
                $badge = null;
                if ($ratingScore >= 8.5) {
                    $badge = ['text' => 'Guest Favorite', 'color' => '#16a34a', 'bg' => '#f0fdf4'];
                } elseif ($ratingScore >= 7.5) {
                    $badge = ['text' => 'Popular Choice', 'color' => '#2563eb', 'bg' => '#eff6ff'];
                }

                return [
                    'id'             => $p->id,
                    'name'           => $p->name,
                    'city'           => $p->city,
                    'property_type'  => $p->type ?? 'Hotel',
                    'primary_image'  => $img,
                    'price_per_night'=> (float) $p->price_per_night,
                    'rating_score'   => $ratingScore,
                    'badge'          => $badge,
                    'url'            => route('hotels.show', $p->id),
                ];
            })
            ->toArray();
    }

    /** Get CityInsightService weather/travel tip for the top-ranked city. */
    private function getCityInsight(string $query, array $locations): ?array
    {
        $city = !empty($locations) ? ($locations[0]['city'] ?? $query) : $query;
        return CityInsightService::getInsights($city);
    }
}
