<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * AutocompleteController — 100% Database-Driven
 *
 * ZERO hardcoded city arrays.
 * All city/district suggestions come from:
 *  1. `locations` table (admin-managed, hierarchy-aware)
 *  2. `properties.city` + `properties.address` (vendor-entered data)
 *
 * Cache strategy: 5 min per query term (low overhead, always fresh enough)
 */
class AutocompleteController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $q      = trim((string) $request->input('q', ''));
        $limit  = min((int) $request->input('limit', 10), 20);

        if (strlen($q) < 1) {
            return response()->json(['success' => true, 'destinations' => [], 'properties' => [], 'data' => ['locations' => [], 'properties' => []]]);
        }

        $cacheKey = 'autocomplete:' . md5(strtolower($q) . ':' . $limit);

        [$destinations, $properties] = Cache::remember($cacheKey, 300, function () use ($q, $limit): array {

            // ─── 1. LOCATION TABLE (admin-managed: districts, upazilas, landmarks) ──
            $locationRows = Location::where(function ($lq) use ($q) {
                    $lq->where('name',    'LIKE', "%{$q}%")
                       ->orWhere('city',  'LIKE', "%{$q}%");
                })
                ->orderByDesc('is_popular')
                ->orderByDesc('id')
                ->limit(6)
                ->get();

            $destinations = $locationRows->map(fn ($loc) => [
                'type'     => 'city',
                'city'     => $loc->name,
                'country'  => $loc->country ?? 'Bangladesh',
                'loc_type' => ucfirst($loc->city ?? 'Location'),
                'title'    => $loc->name . ($loc->city && $loc->city !== $loc->name ? ', ' . $loc->city : '') . ', ' . ($loc->country ?? 'Bangladesh'),
                'subtitle' => ($loc->is_popular ? 'Popular · ' : '') . ($loc->city ?? 'Bangladesh'),
                'lat'      => $loc->latitude,
                'lng'      => $loc->longitude,
            ])->toArray();

            // ─── 2. DISTINCT CITIES FROM LIVE PROPERTIES (vendor-entered) ──────────
            if (count($destinations) < 6) {
                $dbCities = Property::active()
                    ->where(function ($pq) use ($q) {
                        $pq->where('city',    'LIKE', "%{$q}%")
                           ->orWhere('address','LIKE', "%{$q}%");
                    })
                    ->distinct()
                    ->limit(6)
                    ->pluck('city')
                    ->filter()
                    ->toArray();

                $existingNames = array_map('strtolower', array_column($destinations, 'city'));

                foreach ($dbCities as $c) {
                    if (!in_array(strtolower($c), $existingNames, true)) {
                        $destinations[] = [
                            'type'     => 'city',
                            'city'     => $c,
                            'country'  => 'Bangladesh',
                            'loc_type' => 'City / Area',
                            'title'    => $c . ', Bangladesh',
                            'subtitle' => 'City / Area',
                            'lat'      => null,
                            'lng'      => null,
                        ];
                        $existingNames[] = strtolower($c);
                    }
                }
            }

            $destinations = array_slice($destinations, 0, 6);

            // ─── 3. PROPERTY SEARCH (hotel names, addresses, landmarks) ───────────
            $properties = Property::active()
                ->where(function ($pq) use ($q) {
                    $pq->where('name',             'LIKE', "%{$q}%")
                       ->orWhere('city',            'LIKE', "%{$q}%")
                       ->orWhere('address',         'LIKE', "%{$q}%")
                       ->orWhere('nearest_landmark','LIKE', "%{$q}%");
                })
                ->select(['id', 'name', 'slug', 'city', 'address', 'price_per_night', 'primary_image', 'rating_score', 'type'])
                ->limit(5)
                ->get()
                ->map(fn ($p) => [
                    'type'            => 'property',
                    'property_type'   => $p->type ?? 'Hotel',          // e.g. Hotel, Resort, Houseboat
                    'id'              => $p->id,
                    'name'            => $p->name,
                    'city'            => $p->city,
                    'address'         => $p->address,
                    'price_per_night' => (float) $p->price_per_night,
                    'primary_image'   => $p->primary_image
                                            ? (str_starts_with($p->primary_image, 'http')
                                                ? $p->primary_image
                                                : asset('storage/' . ltrim($p->primary_image, '/')))
                                            : null,
                    'rating_score'    => (float) $p->rating_score,
                    'title'           => $p->name,
                    'subtitle'        => ($p->city ? $p->city . ', Bangladesh' : 'Bangladesh'),
                    'url'             => route('hotels.show', $p->id),
                ])
                ->toArray();

            return [$destinations, $properties];
        });

        return response()->json([
            'success'      => true,
            'destinations' => $destinations,
            'properties'   => $properties,
            'data'         => [
                'locations'  => $destinations,
                'properties' => $properties,
            ],
        ]);
    }
}

