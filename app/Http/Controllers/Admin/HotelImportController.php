<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedDestination;
use App\Models\Property;
use App\Services\HotelImporterService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

/**
 * Enterprise Admin OTA Hotel Data Importer Controller
 */
class HotelImportController extends Controller
{
    public function __construct(
        protected HotelImporterService $importerService
    ) {}

    /**
     * Display the Admin Importer Tool Interface.
     */
    public function index(): View
    {
        $destCities   = FeaturedDestination::active()->pluck('city')->toArray();
        $propCities   = Property::whereNotNull('city')->distinct()->pluck('city')->toArray();
        $defaultCities = [
            "Cox's Bazar", "Sajek", "Sylhet", "Dhaka", "Sundarban", "Kuakata",
            "Chittagong", "Bandarban", "Sreemangal", "Saint Martin", "Khagrachhari",
            "Rajshahi", "Barisal", "Rangamati", "Dubai", "Bangkok", "Singapore"
        ];

        $cities = array_values(array_unique(array_filter(array_merge($destCities, $propCities, $defaultCities))));
        sort($cities);

        $propertyTypes = [
            'auto'      => '✨ Auto-Detect from Data',
            'hotel'     => '🏨 Hotel',
            'resort'    => '🏖️ Resort',
            'apartment' => '🏢 Apartment',
            'villa'     => '🏡 Villa',
            'homestay'  => '🏠 Homestay / Guest House',
            'cottage'   => '🪵 Eco Cottage',
        ];

        return view('admin.import.index', compact('cities', 'propertyTypes'));
    }

    /**
     * Handle the Data Import Request (Via API Fetch or Raw JSON Payload).
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'mode'                => 'required|in:api_fetch,json_payload',
            'target_city'         => 'required|string',
            'custom_target_city'  => 'nullable|string',
            'max_limit'           => 'required|string',
            'custom_max_limit'    => 'nullable|integer|min:1|max:1000',
            'override_type'       => 'nullable|string',
            'override_status'     => 'nullable|string',
            'price_multiplier'    => 'nullable|numeric|min:0.1|max:10',
            'endpoint_url'        => 'nullable|required_if:mode,api_fetch|url',
            'cookie_header'       => 'nullable|string',
            'authorization_token' => 'nullable|string',
            'json_payload'        => 'nullable|required_if:mode,json_payload|string',
        ]);

        try {
            $mode = $request->input('mode');

            // Determine Target City (custom input priority)
            $targetCity = trim((string)$request->input('custom_target_city'));
            if (empty($targetCity) || $request->input('target_city') !== 'custom') {
                $targetCity = $request->input('target_city');
            }

            // Determine Max Limit (custom limit priority)
            $maxLimit = (int)$request->input('custom_max_limit');
            if ($maxLimit <= 0 || $request->input('max_limit') !== 'custom') {
                $maxLimit = (int)$request->input('max_limit', 50);
            }

            $options = [
                'override_type'    => $request->input('override_type', 'auto'),
                'override_status'  => $request->input('override_status', Property::STATUS_ACTIVE),
                'price_multiplier' => (float)$request->input('price_multiplier', 1.0),
            ];

            if ($mode === 'api_fetch') {
                $endpoint = $request->input('endpoint_url');
                $cookie   = $request->input('cookie_header');
                $auth     = $request->input('authorization_token');

                $payloadData = $this->importerService->fetchFromApi($endpoint, $cookie, $auth);
            } else {
                $payloadData = $request->input('json_payload');
            }

            $result = $this->importerService->importPayload($payloadData, $targetCity, $maxLimit, $options);

            if ($request->wantsJson()) {
                return response()->json($result);
            }

            if (! $result['success']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'])
                    ->with('import_logs', $result['logs']);
            }

            return redirect()->back()
                ->with('success', "🎉 Import Completed! {$result['imported']} new properties added, {$result['updated']} updated with {$result['total_images']} photos for {$targetCity}.")
                ->with('import_logs', $result['logs']);

        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import Exception: ' . $e->getMessage(),
                    'logs'    => [$e->getMessage()],
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Import Failed: ' . $e->getMessage());
        }
    }
}
