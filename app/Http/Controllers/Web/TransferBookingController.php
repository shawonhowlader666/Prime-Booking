<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AirportTransfer;
use App\Models\TransferBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransferBookingController extends Controller
{
    public function index(Request $request)
    {
        $transfers = AirportTransfer::active()->get();

        return view('pages.transfers', compact('transfers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transfer_id'     => 'required|integer|exists:airport_transfers,id',
            'passenger_name'  => 'required|string|min:2|max:255',
            'passenger_phone' => ['required', 'string', 'max:50', 'regex:/^\+?[0-9\s\-()]{7,20}$/'],
            'passenger_email' => 'required|email|max:255',
            'pickup_datetime' => 'required|date|after:now',
            'flight_number'   => 'nullable|string|max:50',
            'passengers'      => 'required|integer|min:1|max:50',
        ]);

        $transfer = AirportTransfer::active()->find($validated['transfer_id']);
        if (!$transfer) {
            return back()->withInput()->with('error', 'That vehicle is no longer available. Please choose another.');
        }

        if ($transfer->capacity && (int) $validated['passengers'] > (int) $transfer->capacity) {
            return back()->withInput()->with('error', "This vehicle seats {$transfer->capacity} passengers. Please choose a larger vehicle.");
        }

        $ref = 'TRF-' . strtoupper(Str::random(8));

        TransferBooking::create([
            'booking_reference' => $ref,
            'user_id'           => auth()->id(),
            'transfer_id'       => $transfer->id,
            'passenger_name'    => trim($validated['passenger_name']),
            'passenger_phone'   => trim($validated['passenger_phone']),
            'passenger_email'   => strtolower(trim($validated['passenger_email'])),
            'pickup_location'   => $transfer->pickup_location,
            'dropoff_location'  => $transfer->dropoff_location,
            'pickup_datetime'   => $validated['pickup_datetime'],
            'flight_number'     => $validated['flight_number'] ?? null,
            'passengers'        => (int) $validated['passengers'],
            'total_amount'      => (float) $transfer->price,
            'status'            => 'confirmed',
        ]);

        session(["transfer_{$ref}" => true]);

        return redirect()->route('transfers.voucher', $ref)
            ->with('success', "Airport transfer reserved. Reference: {$ref}. Pay the driver on pickup.");
    }
    public function voucher(string $reference)
    {
        $booking = TransferBooking::where('booking_reference', $reference)->firstOrFail();

        {
            // Anti-IDOR Authorization Check
            $isAuthorized = (session("transfer_{$reference}") === true)
                || (auth()->check() && (
                    $booking->user_id === auth()->id()
                    || strtolower((string)auth()->user()->email) === strtolower((string)$booking->passenger_email)
                    || auth()->user()->isAdmin()
                ));

            if (!$isAuthorized) {
                // Obfuscate passenger PII to prevent data scraping
                $rawPhone = (string) $booking->passenger_phone;
                $rawEmail = (string) $booking->passenger_email;
                $booking->passenger_phone = strlen($rawPhone) > 6 ? substr($rawPhone, 0, 4) . '****' . substr($rawPhone, -2) : '****';
                $emailParts = explode('@', $rawEmail);
                $booking->passenger_email = count($emailParts) === 2 ? substr($emailParts[0], 0, 2) . '***@' . $emailParts[1] : '***@***.com';
            }
        }

        return view('pages.transfer-voucher', compact('booking'));
    }
}
