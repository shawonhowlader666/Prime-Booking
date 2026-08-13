<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Reservations & Bookings REST API v1 Routes
| Endpoint: /api/v1/user/bookings ...
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('user')->group(function () {

    // My Bookings List
    Route::get('/bookings', function () {
        $bookings = \App\Models\Booking::where('user_id', auth()->id())
            ->with('property:id,name,city,primary_image,price_per_night,star_rating')
            ->latest()
            ->paginate(10);
        return \App\Http\Resources\V1\BookingResource::collection($bookings)
            ->additional(['success' => true]);
    });

    // Cancel Booking
    Route::patch('/bookings/{reference}/cancel', function ($reference) {
        $booking = \App\Models\Booking::where('booking_reference', $reference)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (in_array($booking->effective_status, ['cancelled', 'completed'])) {
            return response()->json(['success' => false, 'message' => 'This booking cannot be cancelled.'], 422);
        }

        $booking->update(['status' => 'cancelled', 'booking_status' => 'cancelled']);

        return response()->json(['success' => true, 'message' => 'Booking cancelled successfully.']);
    });

});
