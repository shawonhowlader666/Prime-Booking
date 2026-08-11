<?php

namespace App\Http\Controllers\Api\V1\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Search\SearchService;
use App\Traits\ApiResponseTrait;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends Controller
{
    use ApiResponseTrait;

    protected SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * RESTful Hybrid Search Endpoint for Mobile App & Web
     * GET /api/v1/search
     */
    public function search(Request $request)
    {
        $destination = $request->query('destination', '');
        $searchType = $request->query('search_type', 'hotel');
        $checkIn = $request->query('check_in', date('Y-m-d'));
        $checkOut = $request->query('check_out', date('Y-m-d', strtotime('+2 days')));
        $guests = (int)$request->query('guests', 2);
        $minPrice = (float)$request->query('min_price', 0);
        $maxPrice = (float)$request->query('max_price', 100000);
        $starRating = $request->query('star_rating', null);

        $results = $this->searchService->searchHotels([
            'destination' => $destination,
            'search_type' => $searchType,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => $guests,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'star_rating' => $starRating,
        ]);

        return $this->successResponse(
            $results,
            'Search results retrieved successfully.',
            Response::HTTP_OK
        );
    }
}
