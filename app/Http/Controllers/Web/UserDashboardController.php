<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class UserDashboardController extends Controller
{
    public function bookings(Request $request)
    {
        $query = Booking::with(['property', 'room', 'addons']);

        if (auth()->check()) {
            $query->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('guest_email', auth()->user()->email);
            });
        } elseif (session()->has('last_booking_ref')) {
            $query->where('booking_reference', session('last_booking_ref'));
        }

        if ($searchRef = $request->input('reference')) {
            $query->where('booking_reference', 'like', "%{$searchRef}%");
        }

        $bookings = $query->latest()->paginate(10);

        return view('pages.user-bookings', compact('bookings'));
    }

    public function cancelBooking(Request $request, string $reference)
    {
        $booking = Booking::where('booking_reference', $reference)->firstOrFail();

        // Authorization check
        if (auth()->check() && $booking->user_id && $booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized booking cancellation request.');
        }

        if (in_array($booking->booking_status, ['cancelled', 'completed'])) {
            return back()->with('error', 'This booking cannot be cancelled as it is already ' . $booking->booking_status . '.');
        }

        $reason = $request->input('cancellation_reason', 'Cancelled by guest');

        $booking->update([
            'booking_status' => 'cancelled',
            'special_requests' => trim($booking->special_requests . "\n[Cancellation Reason: {$reason}]"),
        ]);

        return back()->with('success', "Booking #{$reference} has been successfully cancelled.");
    }
}
