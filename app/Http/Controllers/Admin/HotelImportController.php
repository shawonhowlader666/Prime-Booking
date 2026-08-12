<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        return view('admin.import.index');
    }

    /**
     * Handle the Data Import Request (Via API Fetch or Raw JSON Payload).
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'mode'                => 'required|in:api_fetch,json_payload',
            'target_city'         => 'required|string',
            'max_limit'           => 'required|integer|min:1|max:200',
            'endpoint_url'        => 'nullable|required_if:mode,api_fetch|url',
            'cookie_header'       => 'nullable|string',
            'authorization_token' => 'nullable|string',
            'json_payload'        => 'nullable|required_if:mode,json_payload|string',
        ]);

        try {
            $mode       = $request->input('mode');
            $targetCity = $request->input('target_city');
            $maxLimit   = (int)$request->input('max_limit', 50);

            if ($mode === 'api_fetch') {
                $endpoint = $request->input('endpoint_url');
                $cookie   = $request->input('cookie_header');
                $auth     = $request->input('authorization_token');

                $payloadData = $this->importerService->fetchFromApi($endpoint, $cookie, $auth);
            } else {
                $payloadData = $request->input('json_payload');
            }

            $result = $this->importerService->importPayload($payloadData, $targetCity, $maxLimit);

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
                ->with('success', "🎉 Import Completed! {$result['imported']} new properties added, {$result['updated']} updated with {$result['total_images']} photos.")
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
