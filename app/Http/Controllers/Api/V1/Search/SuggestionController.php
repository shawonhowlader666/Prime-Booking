<?php

namespace App\Http\Controllers\Api\V1\Search;

use App\Http\Controllers\Controller;
use App\Services\Search\AutoCompleteService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

/**
 * Mobile App & API V1 Suggestion Controller — Enterprise Grade.
 * Powered by AutoCompleteService (Priority Scoring, Trending, City Insights, Levenshtein Typo Tolerance).
 */
class SuggestionController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly AutoCompleteService $service
    ) {}

    public function index(Request $request)
    {
        return $this->suggestions($request);
    }

    /**
     * Real-time Autocomplete Suggestions API (Mobile & Web Parity)
     * GET /api/v1/search/suggestions?q=dhaka&search_type=hotel
     */
    public function suggestions(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        $searchType = (string) $request->query('search_type', 'hotel');
        $limit = min((int) $request->query('limit', 8), 20);

        if (empty($query)) {
            $defaultData = $this->service->getDefaultPayload($searchType);
            return $this->successResponse($defaultData, 'Default destinations and trending retrieved.');
        }

        $results = $this->service->getSuggestions($query, $searchType, $limit);

        // Async log for mobile analytics
        $this->service->logSearch(
            query: $query,
            resolvedCity: $results['locations'][0]['city'] ?? null,
            params: $request->only(['check_in', 'check_out', 'guests', 'rooms', 'search_type']),
            resultCount: count($results['locations']) + count($results['properties'])
        );

        return $this->successResponse($results, 'Suggestions retrieved successfully.');
    }
}
