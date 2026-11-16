<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use App\Models\SystemMessage;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AdminTourPackageController extends Controller
{
    /**
     * Show all tour packages in system.
     * GET /admin/packages
     */
    public function index(): View
    {
        $packages = TourPackage::with('vendor')->latest()->paginate(15);
        $totalCount = TourPackage::count();
        $activeCount = TourPackage::where('status', 'active')->count();

        return view('admin.packages.index', compact('packages', 'totalCount', 'activeCount'));
    }

    /**
     * Show all tour package reservations.
     * GET /admin/packages/bookings
     */
    public function bookings(): View
    {
        $bookings = \App\Models\PackageBooking::with('package', 'user')->latest()->paginate(20);
        return view('admin.packages.bookings', compact('bookings'));
    }

    /**
     * Approve or change package status.
     * POST /admin/packages/{id}/status
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $package = TourPackage::findOrFail($id);

        DB::transaction(function () use ($package) {
            $package->status = ($package->status === 'active') ? 'inactive' : 'active';
            $package->is_active = ($package->status === 'active');
            $package->save();

            Cache::forget('tour_packages_active');

            if ($package->vendor_id && $package->status === 'active') {
                try {
                    SystemMessage::create([
                        'sender_id'    => auth()->id() ?? 1,
                        'recipient_id' => $package->vendor_id,
                        'subject'      => 'Tour Package Approved: ' . $package->title,
                        'message'      => "Congratulations! Your tour package '{$package->title}' has been verified, approved, and is now live on Prime Booking.",
                        'type'         => 'notification',
                        'is_read'      => false,
                    ]);
                } catch (\Throwable $e) {}
            }
        });

        return redirect()->back()->with('success', "Package {$package->title} status updated to {$package->status}.");
    }

    /**
     * Delete a package from system.
     * DELETE /admin/packages/{id}
     */
    public function destroy(int $id): RedirectResponse
    {
        $package = TourPackage::findOrFail($id);

        // Security Guard: Prevent deleting tour packages with active or upcoming traveler bookings
        $hasActive = \App\Models\PackageBooking::where('package_id', $package->id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('travel_date', '>=', now()->toDateString())
            ->exists();

        if ($hasActive) {
            return back()->with('error', "Cannot delete package \"{$package->title}\" because it has active or upcoming traveler reservations.");
        }

        $package->delete();

        return redirect()->back()->with('success', 'Package removed from system successfully.');
    }

    /**
     * Update status of a tour package booking reservation.
     * POST /admin/packages/bookings/{id}/status
     */
    public function updateBookingStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled',
        ]);

        $booking = \App\Models\PackageBooking::findOrFail($id);
        $booking->update(['status' => $validated['status']]);

        return back()->with('success', "Package reservation #{$booking->booking_reference} marked as " . strtoupper($validated['status']) . "!");
    }
}
