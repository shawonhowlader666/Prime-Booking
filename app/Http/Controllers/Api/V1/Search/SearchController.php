<?php

namespace App\Http\Controllers\Api\V1\Search;

use App\Http\Controllers\Controller;
use App\Services\Search\FilterAggregator;
use App\Services\Search\SearchService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected SearchService $searchService,
        protected FilterAggregator $filterAggregator
    ) {}

    /**
     * RESTful Hybrid Search Endpoint for Mobile App & Web
     * GET /api/v1/search
     */
    public function search(Request $request)
    {
        $params = [
            'destination'   => $request->query('destination', ''),
            'division'      => $request->query('division', ''),
            'district'      => $request->query('district', ''),
            'upazila'       => $request->query('upazila', ''),
            'search_type'   => $request->query('search_type', 'hotel'),
            'check_in'      => $request->query('check_in', date('Y-m-d')),
            'check_out'     => $request->query('check_out', date('Y-m-d', strtotime('+2 days'))),
            'guests'        => (int) $request->query('guests', 2),
            'rooms'         => (int) $request->query('rooms', 1),
            'min_price'     => (float) $request->query('min_price', 0),
            'max_price'     => (float) $request->query('max_price', 10000000),
            'star_rating'   => $request->query('star_rating', null),
            'guest_rating'  => $request->query('guest_rating', null),
            'amenities'     => (array) $request->query('amenities', []),
            'property_type' => (array) $request->query('property_type', []),
            'sort_by'       => $request->query('sort_by', 'featured'),
            'lat'           => $request->filled('lat') && is_numeric($request->query('lat')) ? (float) $request->query('lat') : null,
            'lng'           => $request->filled('lng') && is_numeric($request->query('lng')) ? (float) $request->query('lng') : null,
            'radius_km'     => (float) $request->query('radius_km', 30.0),
            'page'          => (int) $request->query('page', 1),
            'per_page'      => min(50, max(1, (int) $request->query('per_page', 15))),
        ];

        $results = $this->searchService->searchHotels($params);

        // Format items for mobile client consumption with distance details
        if (!empty($results['results'])) {
            $formattedResults = [];
            foreach ($results['results'] as $item) {
                $isObj = is_object($item);
                $lat = (float)($isObj ? ($item->latitude ?? 0) : ($item['latitude'] ?? 0));
                $lng = (float)($isObj ? ($item->longitude ?? 0) : ($item['longitude'] ?? 0));
                $dist = $isObj ? ($item->distance_km ?? null) : ($item['distance_km'] ?? null);

                $formattedResults[] = [
                    'id'               => $isObj ? $item->id : $item['id'],
                    'name'             => $isObj ? $item->name : $item['name'],
                    'slug'             => $isObj ? $item->slug : $item['slug'],
                    'type'             => $isObj ? $item->type : $item['type'],
                    'city'             => $isObj ? $item->city : $item['city'],
                    'address'          => $isObj ? $item->address : $item['address'],
                    'price_per_night'  => (float)($isObj ? $item->price_per_night : $item['price_per_night']),
                    'original_price'   => $isObj ? $item->original_price : ($item['original_price'] ?? null),
                    'star_rating'      => (int)($isObj ? ($item->star_rating ?? 0) : ($item['star_rating'] ?? 0)),
                    'rating_score'     => (float)($isObj ? ($item->rating_score ?? 0) : ($item['rating_score'] ?? 0)),
                    'total_reviews'    => (int)($isObj ? ($item->total_reviews ?? 0) : ($item['total_reviews'] ?? 0)),
                    'primary_image'    => $isObj ? ($item->primary_image ?? null) : ($item['primary_image'] ?? null),
                    'latitude'         => $lat,
                    'longitude'        => $lng,
                    'distance_km'      => $dist !== null ? round((float)$dist, 2) : null,
                    'distance_text'    => $dist !== null ? (round((float)$dist, 1) . ' km from your location') : null,
                    'is_featured'      => (bool)($isObj ? ($item->is_featured ?? false) : ($item['is_featured'] ?? false)),
                ];
            }
            $results['mobile_items'] = $formattedResults;
        }

        $results['spatial_context'] = [
            'has_user_coords' => ($params['lat'] !== null && $params['lng'] !== null),
            'user_lat'        => $params['lat'],
            'user_lng'        => $params['lng'],
            'radius_km'       => $params['radius_km'],
        ];

        return $this->successResponse(
            $results,
            'Search results retrieved successfully.',
            Response::HTTP_OK
        );
    }

    /**
     * Dedicated Lightweight Map Properties Endpoint for Mobile App & Web Live Map
     * GET /api/v1/search/map-properties
     */
    public function mapProperties(Request $request)
    {
        $lat = $request->filled('lat') && is_numeric($request->query('lat')) ? (float)$request->query('lat') : null;
        $lng = $request->filled('lng') && is_numeric($request->query('lng')) ? (float)$request->query('lng') : null;
        $radiusKm = (float)$request->query('radius_km', 40.0);
        $destination = (string)$request->query('destination', '');

        $query = \App\Models\Property::active()
            ->select(['id', 'name', 'slug', 'city', 'latitude', 'longitude', 'price_per_night', 'rating_score', 'total_reviews', 'primary_image', 'type', 'free_cancellation']);

        if ($lat !== null && $lng !== null) {
            $query->nearCoordinate($lat, $lng, $radiusKm);
        } elseif (!empty($destination) && !in_array(strtolower($destination), ['near my location', 'near me', 'current location', 'near_my_location'], true)) {
            $query->where('city', 'LIKE', "%{$destination}%");
        }

        $items = $query->limit(100)->get()->map(function ($p) use ($lat, $lng) {
            $dist = ($lat && $lng && isset($p->distance_km)) ? round((float)$p->distance_km, 1) : null;
            return [
                'id'            => $p->id,
                'name'          => $p->name,
                'slug'          => $p->slug,
                'city'          => $p->city,
                'lat'           => (float)$p->latitude,
                'lng'           => (float)$p->longitude,
                'price'         => \App\Services\CurrencyService::format((float)$p->price_per_night),
                'price_raw'     => (float)$p->price_per_night,
                'score'         => (float)$p->rating_score,
                'reviews'       => (int)$p->total_reviews,
                'image'         => $p->primary_image ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=500',
                'url'           => route('property.show', $p->slug ?: $p->id),
                'distance_km'   => $dist,
                'distance_text' => $dist !== null ? "{$dist} km away" : null,
            ];
        });

        return $this->successResponse($items, 'Map properties loaded successfully.', Response::HTTP_OK);
    }

    /**
     * Dedicated Filter Metadata Endpoint for Mobile App & Dynamic Web Filters
     * GET /api/v1/search/filter-metadata
     */
    public function filterMetadata(Request $request)
    {
        $destination = $request->query('destination');
        $data = $this->filterAggregator->getFilterMetadata($destination);

        return $this->successResponse(
            $data,
            'Filter metadata retrieved successfully.',
            Response::HTTP_OK
        );
    }
}
