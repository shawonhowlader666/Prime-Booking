<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class UserDashboardController extends Controller
{
    public function bookings(Request $request)
    {
        if (! auth()->check()) {
            if (session()->has('last_booking_ref')) {
                $bookings = Booking::with(['property', 'room', 'addons'])
                    ->where('booking_reference', session('last_booking_ref'))
                    ->latest()
                    ->paginate(10);
                return view('pages.user-bookings', compact('bookings'));
            }
            return redirect()->route('login')->with('info', 'Please sign in to view your reservations.');
        }

        $query = Booking::with(['property', 'room', 'addons'])
            ->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('guest_email', auth()->user()->email);
            });

        if ($searchRef = $request->input('reference')) {
            $query->where('booking_reference', 'like', "%{$searchRef}%");
        }

        $bookings = $query->latest()->paginate(10);

        return view('pages.user-bookings', compact('bookings'));
    }

    public function cancelBooking(Request $request, string $reference)
    {
        if (! auth()->check()) {
            abort(401, 'Please sign in to cancel your booking.');
        }

        $booking = Booking::where('booking_reference', $reference)->firstOrFail();

        // Strict authorization check — user must be booking owner or admin
        $isOwner = ($booking->user_id === auth()->id()) || (strtolower((string)$booking->guest_email) === strtolower((string)auth()->user()->email));
        $isAdmin = auth()->user()->isAdmin();

        if (! $isOwner && ! $isAdmin) {
            abort(403, 'Unauthorized booking cancellation request.');
        }

        if (in_array($booking->booking_status, ['cancelled', 'completed'], true)) {
            return back()->with('error', 'This booking cannot be cancelled as it is already ' . $booking->booking_status . '.');
        }

        $reason = $request->input('cancellation_reason', 'Cancelled by guest');

        $booking->update([
            'booking_status'   => 'cancelled',
            'status'           => 'cancelled',
            'special_requests' => trim($booking->special_requests . "\n[Cancellation Reason: {$reason}]"),
        ]);

        return back()->with('success', "Booking #{$reference} has been successfully cancelled.");
    }
}
