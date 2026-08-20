<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Repositories\PropertyRepository;
use App\Services\InventoryService;
use App\Services\CouponService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingFlowController extends Controller
{
    public function __construct(
        protected PropertyRepository $properties
    ) {}

    /** GET /checkout?property_id=1&room_id=... */
    public function index(Request $request)
    {
        $propertyId = $request->input('property_id', 1);
        return $this->showForm($request, $propertyId);
    }

    /** GET /book/{propertyId}?room_id=&check_in=&check_out=&guests= */
    public function showForm(Request $request, $propertyId)
    {
        $property = $this->properties->findWithRooms((int) $propertyId);

        if (!$property) {
            abort(404, 'Property not found.');
        }

        $selectedRoomId = $request->input('room_id');
        $selectedRoom   = $selectedRoomId
            ? $property->rooms->firstWhere('id', $selectedRoomId)
            : $property->rooms->first();

        $checkIn  = $request->input('check_in',  date('Y-m-d', strtotime('+1 day')));
        $checkOut = $request->input('check_out', date('Y-m-d', strtotime('+3 days')));
        $guests   = (int) $request->input('guests', 2);
        $nights   = max(1, Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)));

        $pricePerNight = $selectedRoom?->price_per_night ?? $property->price_per_night;
        $subtotal      = $pricePerNight * $nights;
        $taxRate       = 0.075; // 7.5% VAT
        $taxAmount     = round($subtotal * $taxRate);
        $totalPrice    = $subtotal + $taxAmount;

        // Available Addons
        $addons = [
            'airport_transfer' => ['name' => 'Private Airport Pickup & Transfer', 'price' => 1500],
            'daily_breakfast'  => ['name' => 'Buffet Breakfast (All Guests)', 'price' => 500 * $guests * $nights],
            'spa_package'      => ['name' => '60-Min Wellness Spa Voucher', 'price' => 2000],
        ];

        // Auto-apply promotional discount if active via session or query param
        $activePromoCode = $request->input('promo', $request->input('coupon', session('active_promo_code')));
        if (!$activePromoCode && (session('prime_rewards_active') || $request->cookie('prime_rewards_active'))) {
            $activePromoCode = 'PRIMECASH8';
        }

        $appliedDiscount = 0.0;
        if ($activePromoCode) {
            $couponResult = $couponService->validate($activePromoCode, $subtotal);
            if ($couponResult['valid'] ?? false) {
                $appliedDiscount = (float) ($couponResult['discount_amount'] ?? 0.0);
                $totalPrice = max(0, $totalPrice - $appliedDiscount);
            }
        }

        // VIP Automatic Loyalty Discount (AgodaVIP Auto Savings)
        $user = auth()->user();
        $vipService = app(\App\Services\VIPLoyaltyService::class);
        $vipStats = $vipService->getUserTier($user);
        $vipDiscountPercent = (float) ($vipStats['discount_percent'] ?? 0.0);
        $vipDiscountAmount = 0.0;

        if ($vipDiscountPercent > 0 && $appliedDiscount == 0) {
            $vipDiscountAmount = round(($subtotal * $vipDiscountPercent) / 100);
            $appliedDiscount = $vipDiscountAmount;
            $totalPrice = max(0, $totalPrice - $vipDiscountAmount);
        }

        return view('pages.booking-form', compact(
            'property', 'selectedRoom', 'checkIn', 'checkOut',
            'guests', 'nights', 'pricePerNight', 'subtotal', 'taxAmount', 'totalPrice', 'addons', 'user',
            'activePromoCode', 'appliedDiscount', 'vipStats', 'vipDiscountAmount'
        ));
    }

    /** POST /checkout/process */
    public function validateCouponAjax(Request $request, CouponService $couponService)
    {
        $request->validate([
            'code'     => 'required|string|max:50',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $res = $couponService->validateCoupon(
            (string)$request->code,
            (float)$request->subtotal,
            $request->filled('property_id') ? (int)$request->property_id : null
        );

        return response()->json($res);
    }

    /** POST /checkout/process */
    public function process(Request $request, InventoryService $inventoryService, CouponService $couponService, NotificationService $notificationService)
    {
        $propertyId = $request->input('property_id', 1);
        return $this->store($request, $propertyId, $inventoryService, $couponService, $notificationService);
    }

    /** POST /book/{propertyId} — Process booking submission */
    public function store(Request $request, $propertyId, InventoryService $inventoryService, CouponService $couponService, NotificationService $notificationService)
    {
        $request->validate([
            'guest_name'       => 'required|string|max:100',
            'guest_email'      => 'required|email|max:150',
            'guest_phone'      => 'required|string|max:20',
            'check_in'         => 'required|date|after:today',
            'check_out'        => 'required|date|after:check_in',
            'guests'           => 'required|integer|min:1|max:20',
            'payment_method'   => 'required|string|in:bkash,nagad,rocket,card,sslcommerz,cash,pay_at_hotel,bank_transfer',
            'coupon_code'      => 'nullable|string|max:50',
            'special_requests' => 'nullable|string|max:500',
            'addons'           => 'nullable|array',
        ]);

        $property = Property::findOrFail($propertyId);
        $room     = $request->room_id ? Room::where('property_id', $propertyId)->find($request->room_id) : null;

        // 1. Enterprise Availability & Overbooking Verification
        if ($room) {
            $availCheck = $inventoryService->checkAvailability($room->id, $request->check_in, $request->check_out, 1);
            if (!$availCheck['is_available']) {
                return back()->withInput()->with('error', "Sorry! " . ($availCheck['reason'] ?? 'This room is not available for the selected dates.'));
            }
        }

        $checkIn  = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights   = max(1, $checkIn->diffInDays($checkOut));

        $pricePerNight = $room?->price_per_night ?? $property->price_per_night;
        $subtotal      = $pricePerNight * $nights;

        // 2. Coupon Validation & Discount Calculation
        $couponCode     = null;
        $discountAmount = 0.0;
        if ($request->filled('coupon_code')) {
            $cCheck = $couponService->validateCoupon($request->coupon_code, $subtotal, (int)$property->id);
            if ($cCheck['valid']) {
                $couponCode     = $cCheck['code'];
                $discountAmount = (float)$cCheck['discount'];
            }
        }

        // 3. Addons total
        $addonsTotal = 0;
        $selectedAddons = [];

        $availableAddons = [
            'airport_transfer' => ['name' => 'Private Airport Pickup & Transfer', 'price' => 1500],
            'daily_breakfast'  => ['name' => 'Buffet Breakfast (All Guests)', 'price' => 500 * (int)$request->guests * $nights],
            'spa_package'      => ['name' => '60-Min Wellness Spa Voucher', 'price' => 2000],
        ];

        if ($request->has('addons') && is_array($request->addons)) {
            foreach ($request->addons as $key) {
                if (isset($availableAddons[$key])) {
                    $item = $availableAddons[$key];
                    $addonsTotal += $item['price'];
                    $selectedAddons[] = $item;
                }
            }
        }

        $taxAmount  = round(max(0, ($subtotal - $discountAmount + $addonsTotal)) * 0.075);
        $totalPrice = max(0, ($subtotal - $discountAmount + $addonsTotal) + $taxAmount);

        // 4. Platform Commission & Vendor Payout Calculation
        $commissionRate   = 10.00; // 10% standard OTA commission
        $commissionAmount = round(max(0, $subtotal - $discountAmount) * ($commissionRate / 100), 2);
        $vendorPayout     = max(0, ($subtotal - $discountAmount) - $commissionAmount);

        $reference = 'PRM-' . date('Y') . '-' . strtoupper(Str::random(6));

        $isPayAtHotel = in_array($request->payment_method, ['cash', 'pay_at_hotel', 'bank_transfer']);
        $paymentStatus = $isPayAtHotel ? 'unpaid' : 'pending';

        $booking = DB::transaction(function () use (
            $request, $property, $room, $reference, $nights, $pricePerNight, $subtotal, $taxAmount, $totalPrice,
            $selectedAddons, $paymentStatus, $couponCode, $discountAmount, $commissionRate, $commissionAmount, $vendorPayout,
            $inventoryService, $couponService
        ) {
            $specialReq = $request->special_requests;
            if ($couponCode) {
                $specialReq = ($specialReq ? $specialReq . ' | ' : '') . "Coupon Applied: {$couponCode} (-৳{$discountAmount})";
            }

            $b = Booking::create([
                'booking_reference'    => $reference,
                'property_id'          => $property->id,
                'room_id'              => $room?->id,
                'user_id'              => auth()->id(),
                'guest_name'           => $request->guest_name,
                'guest_email'          => $request->guest_email,
                'guest_phone'          => $request->guest_phone,
                'check_in'             => $request->check_in,
                'check_out'            => $request->check_out,
                'guests'               => $request->guests,
                'nights'               => $nights,
                'price_per_night'      => $pricePerNight,
                'subtotal'             => $subtotal,
                'tax_amount'           => $taxAmount,
                'total_price'          => $totalPrice,
                'total_amount'         => $totalPrice,
                'currency'             => 'BDT',
                'payment_method'       => $request->payment_method,
                'payment_status'       => $paymentStatus,
                'status'               => 'confirmed',
                'booking_status'       => 'confirmed',
                'special_requests'     => $specialReq,
            ]);

            foreach ($selectedAddons as $addon) {
                BookingAddon::create([
                    'booking_id' => $b->id,
                    'addon_name' => $addon['name'],
                    'price'      => $addon['price'],
                    'qty'        => 1,
                ]);
            }

            // Lock inventory
            $inventoryService->lockAndDeduct($b);

            // Record coupon usage
            if ($couponCode) {
                $couponService->recordUsage($couponCode);
            }

            return $b;
        });

        // 5. Automated Multi-Channel Notification Dispatch
        try {
            $notificationService->sendBookingConfirmation($booking);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Notification error: " . $e->getMessage());
        }

        // Route to payment gateway based on selected method
        return match ($request->payment_method) {
            'bkash', 'nagad', 'rocket' => redirect()->route('payment.bkash.sandbox-redirect', $booking->booking_reference),
            'card', 'sslcommerz'       => redirect()->route('payment.ssl.sandbox-redirect', $booking->booking_reference),
            default                    => redirect()->route('booking.confirmation', $booking->booking_reference)
                ->with('success', 'Booking reserved successfully! Reference: ' . $booking->booking_reference),
        };
    }

    /** GET /booking/confirmation/{reference} */
    public function confirmation($reference)
    {
        $booking = Booking::where('booking_reference', $reference)
            ->with(['property:id,name,city,address,primary_image,star_rating', 'room:id,name'])
            ->firstOrFail();

        $addons = BookingAddon::where('booking_id', $booking->id)->get();

        return view('pages.booking-confirmation', compact('booking', 'addons'));
    }

    /** GET /booking/voucher/{reference}/download — E-Ticket Voucher */
    public function downloadVoucher($reference)
    {
        $booking = Booking::where('booking_reference', $reference)
            ->with(['property', 'room'])
            ->firstOrFail();

        $addons = BookingAddon::where('booking_id', $booking->id)->get();

        return view('pages.booking-voucher-print', compact('booking', 'addons'));
    }

    /** GET /my-bookings — User's booking history */
    public function myBookings()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with('property:id,name,city,primary_image')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pages.my-bookings', compact('bookings'));
    }
}
