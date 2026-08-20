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
            'guest_name'  => 'nullable|string|max:100',
            'guest_email' => 'nullable|email|max:100',
            'guest_phone' => 'nullable|string|max:20',
        ]);

        $package = TourPackage::findOrFail($validated['package_id']);
        $guests = (int) $validated['guests'];
        $totalAmount = (float) $package->price_per_person * $guests;
        $reference = 'PKG-' . strtoupper(\Illuminate\Support\Str::random(6));

        $user = auth()->user();
        $guestName = $user ? $user->name : ($validated['guest_name'] ?? 'Tour Traveler');
        $guestEmail = $user ? $user->email : ($validated['guest_email'] ?? 'traveler@example.com');
        $guestPhone = $user ? $user->phone : ($validated['guest_phone'] ?? '+880 1700-000000');

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
        $booking = session("pkg_booking_{$reference}");
        if (!$booking) {
            $package = TourPackage::active()->first();
            $booking = [
                'reference'    => $reference,
                'package'      => $package,
                'travel_date'  => now()->addDays(3)->format('Y-m-d'),
                'guests'       => 2,
                'guest_name'   => auth()->user()?->name ?? 'Verified Traveler',
                'guest_email'  => auth()->user()?->email ?? 'traveler@example.com',
                'guest_phone'  => '+880 1700-000000',
                'total_amount' => ($package?->price_per_person ?? 12500) * 2,
                'status'       => 'CONFIRMED',
                'created_at'   => now()->format('d M Y, h:i A'),
            ];
        }

        return view('pages.package-voucher', compact('booking'));
    }
}

