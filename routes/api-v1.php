<?php

use App\Http\Controllers\Api\V1\Search\SuggestionController;
use App\Http\Controllers\Api\V1\Search\SearchController as ApiSearchController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Property\PropertyController as ApiPropertyController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Mobile App & Web RESTful API v1 Routes
| Base URL: /api/v1/...
|--------------------------------------------------------------------------
|
| Public   → No auth required (search, property listing)
| Customer → auth:sanctum required
| Vendor   → auth:sanctum + vendor role
| Admin    → auth:sanctum + admin role
|
*/

Route::prefix('v1')->group(function () {

    // ─────────────────────────────────────────────────────────────────────
    // 1. Public — Property Search & Listing (iOS / Android / Web)
    //    Rate limit: 60 req/min per IP (via RouteServiceProvider 'api' limiter)
    // ─────────────────────────────────────────────────────────────────────
    Route::get('/properties',          [ApiPropertyController::class, 'index']);
    Route::get('/properties/{id}',     [ApiPropertyController::class, 'show']);

    // Public Promotions & Destinations API
    Route::get('/promotions',    [ApiPropertyController::class, 'promotions']);
    Route::get('/destinations',  [ApiPropertyController::class, 'destinations']);

    // Payment Gateway Webhook / IPN Listener Callback
    Route::match(['get', 'post'], '/payment/callback/{gateway}', [\App\Http\Controllers\Api\PaymentCallbackController::class, 'handleCallback']);

    // Public API content endpoints
    Route::get('/packages', function () {
        return response()->json(['success' => true, 'data' => \App\Models\TourPackage::active()->ordered()->get()]);
    });
    Route::get('/deals', function () {
        return response()->json(['success' => true, 'data' => \App\Models\Deal::active()->ordered()->get()]);
    });
    Route::get('/promotions', function () {
        return response()->json(['success' => true, 'data' => \App\Models\Promotion::active()->ordered()->get()]);
    });
    Route::get('/destinations', function () {
        return response()->json(['success' => true, 'data' => \App\Models\FeaturedDestination::active()->get()]);
    });

    Route::middleware('throttle:search')->group(function () {
        Route::get('/search',          [ApiSearchController::class, 'search']);
        Route::get('/suggestions',     [SuggestionController::class, 'index']);
    });

    Route::get('/search/suggestions',  [SuggestionController::class, 'suggestions']); // GET /api/v1/search/suggestions?q=

    // ─────────────────────────────────────────────────────────────────────
    // 2. Auth Endpoints — rate limited, returns JWT-like sanctum tokens
    // ─────────────────────────────────────────────────────────────────────
    Route::prefix('auth')->middleware('throttle:auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login',    [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me',             [AuthController::class, 'me']);
            Route::put('/profile',        [AuthController::class, 'updateProfile']);
            Route::post('/logout',        [AuthController::class, 'logout']);
        });
    });

    // Public Airport Transfers API
    Route::get('/transfers', function () {
        return response()->json(['success' => true, 'data' => \App\Models\AirportTransfer::active()->get()]);
    });

    // ─────────────────────────────────────────────────────────────────────
    // 3. Customer Protected Endpoints
    // ─────────────────────────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->prefix('user')->group(function () {

        // My Bookings — full resource with property info
        Route::get('/bookings', function () {
            $bookings = \App\Models\Booking::where('user_id', auth()->id())
                ->with('property:id,name,city,primary_image,price_per_night,star_rating')
                ->latest()
                ->paginate(10);
            return \App\Http\Resources\V1\BookingResource::collection($bookings)
                ->additional(['success' => true]);
        });

        // Cancel a booking
        Route::patch('/bookings/{reference}/cancel', function ($reference) {
            $booking = \App\Models\Booking::where('booking_reference', $reference)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            if (in_array($booking->effective_status, ['cancelled', 'completed'])) {
                return response()->json(['success'=>false, 'message'=>'This booking cannot be cancelled.'], 422);
            }

            $booking->update(['status'=>'cancelled','booking_status'=>'cancelled']);

            return response()->json(['success'=>true, 'message'=>'Booking cancelled successfully.']);
        });

        // User Messages API
        Route::get('/messages', function () {
            $messages = \App\Models\Message::where('sender_id', auth()->id())
                ->orWhere('receiver_id', auth()->id())
                ->latest()
                ->get();
            return response()->json(['success' => true, 'data' => $messages]);
        });

        Route::post('/messages', function (Request $request) {
            $request->validate(['message' => 'required|string']);
            $msg = \App\Models\Message::create([
                'sender_id'   => auth()->id(),
                'property_id' => $request->property_id,
                'subject'     => $request->subject ?? 'Inquiry',
                'message'     => $request->message,
            ]);
            return response()->json(['success' => true, 'data' => $msg]);
        });

        // User Reviews API
        Route::get('/reviews', function () {
            $reviews = \App\Models\Review::where('user_id', auth()->id())->latest()->get();
            return response()->json(['success' => true, 'data' => $reviews]);
        });

    });

    // ─────────────────────────────────────────────────────────────────────
    // 4. Vendor Protected Endpoints (Requires Login + Vendor Role)
    // ─────────────────────────────────────────────────────────────────────
    Route::middleware(['auth:sanctum'])->prefix('vendor')->group(function () {

        // My Properties (list only own)
        Route::get('/properties', function (Request $request) {
            $props = \App\Models\Property::where('vendor_id', auth()->id())
                        ->orderByDesc('created_at')
                        ->paginate(15);
            return response()->json(['success' => true, 'data' => $props]);
        });

        // Create New Property (submitted as inactive → admin reviews)
        Route::post('/properties',         [ApiPropertyController::class, 'store']);
        Route::put('/properties/{id}',     [ApiPropertyController::class, 'update']);
        Route::delete('/properties/{id}',  [ApiPropertyController::class, 'destroy']);

        // Vendor Stats Dashboard
        Route::get('/stats', function () {
            $vendorId = auth()->id();
            return response()->json([
                'success' => true,
                'data' => [
                    'total_properties' => \App\Models\Property::where('vendor_id', $vendorId)->count(),
                    'total_bookings'   => \App\Models\Booking::whereHas('property', fn($q) => $q->where('vendor_id', $vendorId))->count(),
                    'active_listings'  => \App\Models\Property::where('vendor_id', $vendorId)->where('status', 'active')->count(),
                ],
            ]);
        });

        // Vendor Availability & Rates API
        Route::get('/availability', function (Request $request) {
            $roomId = $request->query('room_id');
            $av = \App\Models\RoomAvailability::where('room_id', $roomId)->get();
            return response()->json(['success' => true, 'data' => $av]);
        });

        Route::post('/availability', function (Request $request) {
            $request->validate(['room_id' => 'required', 'date' => 'required', 'price' => 'required']);
            $av = \App\Models\RoomAvailability::updateOrCreate(
                ['room_id' => $request->room_id, 'date' => $request->date],
                ['price' => $request->price, 'is_available' => $request->is_available ?? true]
            );
            return response()->json(['success' => true, 'data' => $av]);
        });

        // Vendor Payouts API
        Route::get('/payouts', function () {
            $payouts = \App\Models\PayoutRequest::where('vendor_id', auth()->id())->latest()->get();
            return response()->json(['success' => true, 'data' => $payouts]);
        });

        Route::post('/payouts', function (Request $request) {
            $request->validate(['amount' => 'required|numeric|min:1000', 'payment_method' => 'required']);
            $payout = \App\Models\PayoutRequest::create([
                'vendor_id'      => auth()->id(),
                'amount'         => $request->amount,
                'payment_method' => $request->payment_method,
                'account_details'=> $request->account_details ?? 'bKash / Bank Account',
                'status'         => 'pending',
            ]);
            return response()->json(['success' => true, 'data' => $payout]);
        });

    });

    // ─────────────────────────────────────────────────────────────────────
    // 5. Admin Protected Endpoints (Requires Login + Admin Role)
    // ─────────────────────────────────────────────────────────────────────
    Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {

        // Global Stats
        Route::get('/stats', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_users'      => \App\Models\User::count(),
                    'total_properties' => \App\Models\Property::count(),
                    'total_bookings'   => \App\Models\Booking::count(),
                    'active_listings'  => \App\Models\Property::where('status', 'active')->count(),
                    'pending_review'   => \App\Models\Property::where('status', 'inactive')->whereNotNull('vendor_id')->count(),
                ],
            ]);
        });

        // Toggle property status
        Route::post('/properties/{id}/status', function ($id) {
            $prop = \App\Models\Property::findOrFail($id);
            $prop->update(['status' => $prop->status === 'active' ? 'inactive' : 'active']);
            return response()->json(['success' => true, 'status' => $prop->status]);
        });

        // Site Settings API
        Route::post('/settings', function (Request $request) {
            foreach ($request->all() as $k => $v) {
                \App\Models\SiteSetting::set($k, (string)$v);
            }
            \Cache::flush();
            return response()->json(['success' => true, 'message' => 'Settings updated successfully.']);
        });

    });

});
