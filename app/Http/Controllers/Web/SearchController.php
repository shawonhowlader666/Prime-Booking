<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\SearchRequest;
use App\Services\Search\SearchService;
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
            // Filter sidebar data
            'availableCities' => $availableCities,
            'priceRange'      => $priceRange,
            'filterCounts'    => $filterCounts,
        ]);
    }
}
