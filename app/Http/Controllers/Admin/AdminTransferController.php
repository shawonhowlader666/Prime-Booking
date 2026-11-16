<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AirportTransfer;
use App\Models\TransferBooking;
use Illuminate\Http\Request;

class AdminTransferController extends Controller
{
    public function index()
    {
        $company = config('company');

        // Seed default routes if empty
        if (AirportTransfer::count() === 0) {
            AirportTransfer::create([
                'pickup_location'  => 'Hazrat Shahjalal Int\'l Airport (DAC)',
                'dropoff_location' => 'Gulshan-2 / Banani, Dhaka',
                'vehicle_type'     => 'Sedan (Toyota Allion / Premio)',
                'price'            => 2500.00,
                'capacity'         => 4,
                'luggage_capacity' => 2,
                'is_active'        => true,
            ]);
            AirportTransfer::create([
                'pickup_location'  => 'Hazrat Shahjalal Int\'l Airport (DAC)',
                'dropoff_location' => 'Radisson Blu / Uttara, Dhaka',
                'vehicle_type'     => 'Microbus (Toyota HiAce)',
                'price'            => 4500.00,
                'capacity'         => 8,
                'luggage_capacity' => 6,
                'is_active'        => true,
            ]);
            AirportTransfer::create([
                'pickup_location'  => 'Cox\'s Bazar Airport (CXB)',
                'dropoff_location' => 'Inani Beach Resorts, Cox\'s Bazar',
                'vehicle_type'     => 'SUV (Toyota Prado)',
                'price'            => 3800.00,
                'capacity'         => 5,
                'luggage_capacity' => 4,
                'is_active'        => true,
            ]);
        }

        $transfers = AirportTransfer::latest()->paginate(15);
        $bookings  = TransferBooking::with('transfer')->latest()->take(10)->get();

        return view('admin.transfers.index', compact('company', 'transfers', 'bookings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pickup_location'  => 'required|string|max:255',
            'dropoff_location' => 'required|string|max:255',
            'vehicle_type'     => 'required|string|max:255',
            'price'            => 'required|numeric|min:0',
            'capacity'         => 'required|integer|min:1',
            'luggage_capacity' => 'required|integer|min:0',
        ]);

        AirportTransfer::create($validated + ['is_active' => true]);

        return back()->with('success', 'Airport Transfer route added successfully!');
    }

    public function update(Request $request, $id)
    {
        $transfer = AirportTransfer::findOrFail($id);

        $validated = $request->validate([
            'pickup_location'  => 'required|string|max:255',
            'dropoff_location' => 'required|string|max:255',
            'vehicle_type'     => 'required|string|max:255',
            'price'            => 'required|numeric|min:0',
            'capacity'         => 'required|integer|min:1',
            'luggage_capacity' => 'required|integer|min:0',
            'is_active'        => 'required|boolean',
        ]);

        $transfer->update($validated);

        return back()->with('success', 'Transfer route updated successfully!');
    }

    public function toggleStatus($id)
    {
        $transfer = AirportTransfer::findOrFail($id);
        $transfer->is_active = !$transfer->is_active;
        $transfer->save();

        return back()->with('success', 'Transfer status toggled successfully!');
    }

    public function bookings()
    {
        $bookings = TransferBooking::with(['transfer', 'user'])->latest()->paginate(20);
        return view('admin.transfers.bookings', compact('bookings'));
    }

    public function updateBookingStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled',
        ]);

        $booking = TransferBooking::findOrFail($id);
        $booking->update(['status' => $request->status]);

        return back()->with('success', "Transfer booking #{$booking->booking_reference} marked as " . strtoupper($request->status) . "!");
    }

    public function destroy($id)
    {
        $transfer = AirportTransfer::findOrFail($id);

        // Security Guard: Prevent deleting routes with active or upcoming passenger bookings
        $hasActive = TransferBooking::where('transfer_id', $id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('pickup_datetime', '>=', now())
            ->exists();

        if ($hasActive) {
            return back()->with('error', "Cannot delete transfer route because it has active or upcoming passenger reservations. Please fulfill or reassign them first.");
        }

        $transfer->delete();

        return back()->with('success', 'Transfer route deleted successfully!');
    }
}
