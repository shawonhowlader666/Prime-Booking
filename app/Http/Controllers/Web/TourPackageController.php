<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourPackageController extends Controller
{
    /**
     * Show all tour packages with search & destination filters.
     * GET /packages
     */
    public function index(Request $request): View
    {
        $query = TourPackage::active();

        if ($request->filled('destination')) {
            $query->destination($request->string('destination')->trim()->toString());
        }

        if ($request->filled('sort')) {
            match($request->string('sort')->toString()) {
                'price_low'  => $query->orderBy('price_per_person', 'asc'),
                'price_high' => $query->orderBy('price_per_person', 'desc'),
                'duration'   => $query->orderBy('duration_days', 'desc'),
                default      => $query->latest(),
            };
        } else {
            $query->latest();
        }

        $packages = $query->paginate(9);
        $destinations = TourPackage::active()->distinct()->pluck('destination');

        return view('pages.packages.index', compact('packages', 'destinations'));
    }

    /**
     * Show single tour package details.
     * GET /packages/{slug}
     */
    public function show(string $slug): View
    {
        $package = TourPackage::where('slug', $slug)->firstOrFail();
        $relatedPackages = TourPackage::active()
            ->where('id', '!=', $package->id)
            ->where('destination', $package->destination)
            ->limit(3)
            ->get();

        if ($relatedPackages->isEmpty()) {
            $relatedPackages = TourPackage::active()
                ->where('id', '!=', $package->id)
                ->limit(3)
                ->get();
        }

        return view('pages.packages.show', compact('package', 'relatedPackages'));
    }

    /**
     * Book a tour package.
     * POST /packages/book
     */
    public function book(Request $request)
    {
        $validated = $request->validate([
            'package_id'  => 'required|exists:tour_packages,id',
            'travel_date' => 'required|date|after_or_equal:today',
            'guests'      => 'required|integer|min:1|max:20',
            'guest_name'  => [auth()->check() ? 'nullable' : 'required', 'string', 'min:2', 'max:100'],
            'guest_email' => [auth()->check() ? 'nullable' : 'required', 'email', 'max:100'],
            'guest_phone' => [auth()->check() && auth()->user()->phone ? 'nullable' : 'required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-()]{7,20}$/'],
            'special_requests' => 'nullable|string|max:500',
        ]);

        $package = TourPackage::active()->findOrFail($validated['package_id']);
        $guests = (int) $validated['guests'];
        $totalAmount = (float) $package->price_per_person * $guests;
        $reference = 'PKG-' . strtoupper(\Illuminate\Support\Str::random(6));

        $user = auth()->user();
        $guestName  = trim((string) ($validated['guest_name'] ?? $user?->name));
        $guestEmail = strtolower(trim((string) ($validated['guest_email'] ?? $user?->email)));
        $guestPhone = trim((string) ($validated['guest_phone'] ?? $user?->phone));

        \Illuminate\Support\Facades\DB::transaction(function () use (
            $reference, $package, $user, $guestName, $guestEmail, $guestPhone, $validated, $totalAmount
        ) {
            \App\Models\PackageBooking::create([
                'booking_reference' => $reference,
                'package_id'        => $package->id,
                'user_id'           => $user?->id,
                'guest_name'        => $guestName,
                'guest_email'       => $guestEmail,
                'guest_phone'       => $guestPhone,
                'travel_date'       => $validated['travel_date'],
                'guests'            => (int) $validated['guests'],
                'total_amount'      => $totalAmount,
                'payment_method'    => 'pay_on_arrival',
                'payment_status'    => 'pending',
                'status'            => 'confirmed',
                'special_requests'  => $validated['special_requests'] ?? null,
            ]);
        });

        $bookingData = [
            'reference'        => $reference,
            'package'          => $package,
            'travel_date'      => $validated['travel_date'],
            'guests'           => $guests,
            'guest_name'       => $guestName,
            'guest_email'      => $guestEmail,
            'guest_phone'      => $guestPhone,
            'total_amount'     => $totalAmount,
            'status'           => 'CONFIRMED',
            'created_at'       => now()->format('d M Y, h:i A'),
        ];

        session()->put("pkg_booking_{$reference}", $bookingData);

        return redirect()->route('packages.voucher', $reference)->with('success', "🎉 Your tour package booking for {$package->title} has been confirmed!");
    }

    /**
     * Show package voucher / confirmation.
     * GET /packages/voucher/{reference}
     */
    public function voucher(string $reference): View
    {
        $dbBooking = \App\Models\PackageBooking::with('package')->where('booking_reference', $reference)->first();

        if ($dbBooking) {
            // Anti-IDOR Authorization Check
            $isAuthorized = (session("pkg_booking_{$reference}") !== null)
                || (auth()->check() && (
                    $dbBooking->user_id === auth()->id()
                    || strtolower((string)auth()->user()->email) === strtolower((string)$dbBooking->guest_email)
                    || auth()->user()->isAdmin()
                ));

            $guestName  = $dbBooking->guest_name;
            $guestPhone = $dbBooking->guest_phone;
            $guestEmail = $dbBooking->guest_email;

            if (!$isAuthorized) {
                // Mask sensitive PII
                $guestPhone = strlen($guestPhone) > 6 ? substr($guestPhone, 0, 4) . '****' . substr($guestPhone, -2) : '****';
                $parts = explode('@', $guestEmail);
                $guestEmail = count($parts) === 2 ? substr($parts[0], 0, 2) . '***@' . $parts[1] : '***@***.com';
            }

            $booking = [
                'reference'    => $dbBooking->booking_reference,
                'package'      => $dbBooking->package,
                'travel_date'  => $dbBooking->travel_date->format('Y-m-d'),
                'guests'       => $dbBooking->guests,
                'guest_name'   => $guestName,
                'guest_email'  => $guestEmail,
                'guest_phone'  => $guestPhone,
                'total_amount' => $dbBooking->total_amount,
                'status'       => strtoupper($dbBooking->status),
                'created_at'   => $dbBooking->created_at->format('d M Y, h:i A'),
            ];
        } else {
            $booking = session("pkg_booking_{$reference}");
            abort_if(!$booking, 404);
        }

        return view('pages.package-voucher', compact('booking'));
    }
}

