<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Search\AutoCompleteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AutocompleteController — Thin HTTP adapter over AutoCompleteService.
 *
 * Responsibilities:
 *  - Validate/sanitize input
 *  - Delegate ALL business logic to AutoCompleteService
 *  - Return consistent JSON response shape
 *  - Log search asynchronously (fire-and-forget via queue)
 *
 * Routes:
 *  GET /api/search/autocomplete?q=khulna&search_type=hotel
 *  POST /api/search/log-query   (selection logging)
 */
class AutocompleteController extends Controller
{
    public function __construct(
        private readonly AutoCompleteService $service
    ) {}

    /**
     * Main autocomplete endpoint.
     * Called on every debounced keystroke (180ms) from the search card.
     */
    public function search(Request $request): JsonResponse
    {
        $q          = trim((string) $request->input('q', ''));
        $searchType = $request->input('search_type', 'hotel');
        $limit      = min((int) $request->input('limit', 8), 20);

        if (strlen($q) < 1) {
            // Default payload: trending + BD grid + personalized (if logged in)
            $payload = $this->service->getDefaultPayload($searchType);
        } else {
            // Live typing: scored locations + properties + city insight
            $payload = $this->service->getSuggestions($q, $searchType, $limit);

            // Log asynchronously — zero latency impact
            $this->service->logSearch(
                query:       $q,
                resolvedCity: $payload['locations'][0]['city'] ?? null,
                params:      $request->only(['check_in', 'check_out', 'guests', 'rooms', 'search_type']),
                resultCount: count($payload['locations']) + count($payload['properties']),
            );
        }

        return response()->json([
            'success' => true,
            'data'    => $payload,
            // Backward-compat keys for the existing JS that reads data.locations / data.properties
            'locations'  => $payload['locations']  ?? [],
            'properties' => $payload['properties'] ?? [],
        ]);
    }

    /**
     * Log when user selects a destination (clicked on a suggestion pill).
     * Called via POST from JS — fire-and-forget from client side.
     */
    public function logSelection(Request $request): JsonResponse
    {
        $this->service->logSearch(
            query:        trim((string) $request->input('query', '')),
            resolvedCity: $request->input('city'),
            params:       $request->only(['check_in', 'check_out', 'guests', 'rooms', 'search_type']),
            resultCount:  (int) $request->input('result_count', 1),
        );

        return response()->json(['success' => true]);
    }
}
