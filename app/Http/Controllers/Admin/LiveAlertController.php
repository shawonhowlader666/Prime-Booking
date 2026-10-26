<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Payout;
use App\Models\Inquiry;
use Carbon\Carbon;

class LiveAlertController extends Controller
{
    /**
     * Polling endpoint for real-time admin alerts & audio chime triggers.
     */
    public function poll(Request $request)
    {
        $lastChecked = $request->query('last_checked');
        $lastCheckedTime = $lastChecked ? Carbon::parse($lastChecked) : Carbon::now()->subMinutes(5);

        $pendingProperties = Property::where('status', 'pending')->count();
        $pendingPayouts    = Payout::where('status', 'pending')->count();
        $pendingBookings   = Booking::whereIn('status', ['pending', 'confirmed'])->where('created_at', '>=', $lastCheckedTime)->count();
        $pendingInquiries  = Inquiry::whereNull('replied_at')->count();

        $latestBooking = Booking::with('property:id,name')
            ->latest()
            ->first(['id', 'booking_reference', 'property_id', 'guest_name', 'total_price', 'status', 'created_at']);

        $hasNewAlert = Booking::where('created_at', '>', $lastCheckedTime)->exists();

        $userId = auth()->id() ? (int)auth()->id() : null;
        $sidebarCounts = \App\Services\SidebarBadgeService::getAdminCounts($userId, true);

        return response()->json([
            'status'             => 'success',
            'timestamp'          => Carbon::now()->toIso8601String(),
            'pending_properties' => $pendingProperties,
            'pending_payouts'    => $pendingPayouts,
            'pending_inquiries'  => $pendingInquiries,
            'total_alerts'       => $sidebarCounts['total_alerts'] ?? ($pendingProperties + $pendingPayouts + $pendingInquiries),
            'counts'             => $sidebarCounts,
            'play_chime'         => $hasNewAlert,
            'latest_booking'     => $latestBooking ? [
                'ref'           => $latestBooking->booking_reference,
                'guest'         => $latestBooking->guest_name,
                'property'      => $latestBooking->property?->name ?? 'Hotel Stay',
                'amount'        => number_format((float)$latestBooking->total_price),
                'time_ago'      => $latestBooking->created_at->diffForHumans(),
            ] : null,
        ]);
    }
}
