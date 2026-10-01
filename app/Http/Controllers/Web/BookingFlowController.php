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

        $pricingEngine = app(\App\Services\DynamicPricingService::class);
        if ($selectedRoom) {
            $stayPricing   = $pricingEngine->calculateStayPrice($selectedRoom, $checkIn, $checkOut, 1);
            $subtotal      = (float) $stayPricing['final_price'];
            $pricePerNight = (float) $stayPricing['avg_nightly_rate'];
        } else {
            $pricePerNight = (float) ($property->price_per_night ?? 1500);
            $subtotal      = $pricePerNight * $nights;
        }

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
        $couponService = app(\App\Services\CouponService::class);
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

        // PointsMAX Active Primary Program (Earn Miles with Booking)
        $pointsmaxPrograms = $user && $user->pointsmax_programs
            ? json_decode($user->pointsmax_programs, true)
            : (session('pointsmax_programs_' . ($user?->id ?? 'guest')) ?: []);
        $primaryPointsmax = collect($pointsmaxPrograms)->firstWhere('is_primary', true) ?: collect($pointsmaxPrograms)->first();
        $earnedMiles = $primaryPointsmax ? min(6000, max(250, round($subtotal * 0.15))) : 0;

        // Prime Rewards Points (Earn & Redeem)
        $rewardService = app(\App\Services\RewardPointService::class);
        $rewardSummary = $rewardService->getUserRewardSummary($user);
        $earnablePoints = $rewardService->calculatePoints($totalPrice);

        // ── 15-Minute Agoda-Style Room Hold & Idempotency Token ───────────────
        $holdToken = session('checkout_hold_token') ?: Str::uuid()->toString();
        session(['checkout_hold_token' => $holdToken]);

        $inventoryService = app(\App\Services\InventoryService::class);
        if ($selectedRoom) {
            $inventoryService->holdRoom((int)$selectedRoom->id, (string)$checkIn, (string)$checkOut, $holdToken, 15);
        }

        $holdData = \Illuminate\Support\Facades\Cache::get("room_hold_token_{$holdToken}");
        $holdSecondsRemaining = $holdData ? max(0, $holdData['expires'] - now()->timestamp) : 900;
        $idempotencyToken = Str::random(32);
        session(['checkout_idempotency_token' => $idempotencyToken]);

        return view('pages.booking-form', compact(
            'property', 'selectedRoom', 'checkIn', 'checkOut',
            'guests', 'nights', 'pricePerNight', 'subtotal', 'taxAmount', 'totalPrice', 'addons', 'user',
            'activePromoCode', 'appliedDiscount', 'vipStats', 'vipDiscountAmount', 'primaryPointsmax', 'earnedMiles',
            'rewardSummary', 'earnablePoints', 'holdToken', 'holdSecondsRemaining', 'idempotencyToken'
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
            'check_in'         => 'required|date|after_or_equal:today',
            'check_out'        => 'required|date|after:check_in',
            'guests'           => 'required|integer|min:1|max:20',
            'payment_method'   => 'required|string|in:bkash,nagad,rocket,card,sslcommerz,cash,pay_at_hotel,bank_transfer',
            'coupon_code'      => 'nullable|string|max:50',
            'special_requests' => 'nullable|string|max:500',
            'addons'           => 'nullable|array',
        ], [
            'check_in.after_or_equal' => 'Check-in date cannot be in the past. Please select today or a future date.',
            'check_out.after'         => 'Check-out date must be at least 1 night after the check-in date.',
        ]);

        // 0. Enterprise Idempotency Protection (Double-charge & Double-booking prevention)
        $property = Property::findOrFail($propertyId);
        $room = $request->filled('room_id') ? Room::where('property_id', $property->id)->find($request->room_id) : null;
        if (!$room) {
            $room = Room::where('property_id', $property->id)->first();
        }

        $idempotencyToken = $request->input('idempotency_token');
        if ($idempotencyToken) {
            $lockKey = "idempotency_checkout_{$idempotencyToken}";
            if (!\Illuminate\Support\Facades\Cache::add($lockKey, 1, 30)) {
                return back()->withInput()->with('error', 'Your booking request is already being processed. Please do not submit multiple times.');
            }
        }

        // 0.1 AI Fraud & Booking Abuse Risk Evaluation
        $fraudDetector = app(\App\Services\AI\FraudDetector::class);
        $fraudRisk = $fraudDetector->evaluateBooking($request->all(), auth()->user());
        if ($fraudRisk['decision'] === 'REJECT_OR_MANUAL_REVIEW') {
            return back()->withInput()->with('error', 'Unable to process your request at this time. Please contact customer support for assistance.');
        }

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

        $pricingEngine = app(\App\Services\DynamicPricingService::class);
        if ($room) {
            $stayPricing   = $pricingEngine->calculateStayPrice($room, $request->check_in, $request->check_out, 1);
            $subtotal      = (float) $stayPricing['final_price'];
            $pricePerNight = (float) $stayPricing['avg_nightly_rate'];
        } else {
            $pricePerNight = (float) ($property->price_per_night ?? 1500);
            $subtotal      = $pricePerNight * $nights;
        }

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

        // 4. Platform Commission & Vendor Payout Calculation (Dynamic per-property rate)
        $commissionRate   = (float) ($property->commission_rate ?? 12.00);
        $commissionAmount = round(max(0, $subtotal - $discountAmount) * ($commissionRate / 100), 2);
        $vendorPayout     = max(0, ($subtotal - $discountAmount) - $commissionAmount);

        $reference = 'PRM-' . date('Y') . '-' . strtoupper(Str::random(6));

        $isPayAtHotel = in_array($request->payment_method, ['cash', 'pay_at_hotel', 'bank_transfer']);
        $paymentStatus = $isPayAtHotel ? 'unpaid' : 'pending';

        try {
            $booking = DB::transaction(function () use (
                $request, $property, $room, $reference, $nights, $pricePerNight, $subtotal, $taxAmount, $totalPrice,
                $selectedAddons, $paymentStatus, $couponCode, $discountAmount, $commissionRate, $commissionAmount, $vendorPayout,
                $inventoryService, $couponService
            ) {
                if ($room) {
                    // Enterprise Pessimistic Concurrency Lock (Agoda pattern)
                    $lockedRoom = Room::where('id', $room->id)->lockForUpdate()->first();
                    if ($lockedRoom) {
                        $activeBookingsCount = Booking::where('room_id', $lockedRoom->id)
                            ->where(function ($q) {
                                $q->where('status', 'confirmed')
                                  ->orWhere('booking_status', 'confirmed')
                                  ->orWhere('status', 'pending');
                            })
                            ->whereDate('check_in', '<', $request->check_out)
                            ->whereDate('check_out', '>', $request->check_in)
                            ->count();

                        $totalCap = (int) ($lockedRoom->total_rooms ?? 10);
                        if ($activeBookingsCount >= $totalCap) {
                            throw new \Exception('Sorry, the last room was just reserved by another guest. Please select another date or room.');
                        }
                    }
                }

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
                    'coupon_code'          => $couponCode,
                    'discount_amount'      => $discountAmount,
                    'commission_rate'      => $commissionRate,
                    'commission_amount'    => $commissionAmount,
                    'vendor_payout_amount' => $vendorPayout,
                    'vendor_payout'        => $vendorPayout,
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
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Booking transaction race conflict for property {$property->id}: " . $e->getMessage());
            return back()->withInput()->with('error', $e->getMessage() ?: 'Booking could not be finalized. Please try again.');
        }

        // Release temporary checkout hold
        if ($request->filled('hold_token')) {
            $inventoryService->releaseHold((string)$request->hold_token);
        }

        // 5. Automated Multi-Channel Notification Dispatch & Rewards Point Auto-Credit
        try {
            $notificationService->sendBookingConfirmation($booking);
            app(\App\Services\RewardPointService::class)->creditBookingPoints($booking);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Booking post-processing error: " . $e->getMessage());
        }

        // Route to payment gateway based on selected method
        // Also record the initiated transaction in payment_transactions for audit
        $payVerify = app(\App\Services\PaymentVerificationService::class);
        return match ($request->payment_method) {
            'bkash', 'nagad', 'rocket' => redirect()->route('payment.bkash.sandbox-redirect', $booking->booking_reference),
            'card', 'sslcommerz'       => redirect()->route('payment.ssl.sandbox-redirect', $booking->booking_reference),
            default => redirect()->route('booking.confirmation', $booking->booking_reference)
                ->with('success', 'Booking reserved successfully! Reference: ' . $booking->booking_reference),
        };
    }

    /** GET /booking/confirmation/{reference} */
    public function confirmation($reference)
    {
        $booking = Booking::where('booking_reference', $reference)
            ->with(['property', 'room'])
            ->firstOrFail();

        $this->authorizeBookingAccess($booking);

        $addons = BookingAddon::where('booking_id', $booking->id)->get();
        $room = $booking->room;
        $property = $booking->property;

        return view('pages.booking-confirmation', compact('booking', 'addons', 'room', 'property'));
    }

    /** GET /booking/voucher/{reference}/download — E-Ticket Voucher */
    public function downloadVoucher($reference)
    {
        $booking = Booking::where('booking_reference', $reference)
            ->with(['property', 'room'])
            ->firstOrFail();

        $this->authorizeBookingAccess($booking);

        $addons = BookingAddon::where('booking_id', $booking->id)->get();

        return view('pages.booking-voucher-print', compact('booking', 'addons'));
    }

    /** GET /booking/invoice/{reference} — Official Tax Invoice / Payment Receipt */
    public function downloadInvoice($reference)
    {
        $booking = Booking::where('booking_reference', $reference)
            ->with(['property', 'room'])
            ->firstOrFail();

        $this->authorizeBookingAccess($booking);

        $addons = BookingAddon::where('booking_id', $booking->id)->get();

        return view('pages.booking-invoice-print', compact('booking', 'addons'));
    }

    /** Ensure only booking owner, vendor, admin, or session guest can access booking documents */
    private function authorizeBookingAccess(Booking $booking): void
    {
        if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->role === 'super_admin')) {
            return;
        }

        if (auth()->check() && auth()->user()->isVendor() && $booking->property?->vendor_id === auth()->id()) {
            return;
        }

        if (auth()->check() && ($booking->user_id === auth()->id() || strtolower((string)$booking->guest_email) === strtolower((string)auth()->user()->email))) {
            return;
        }

        if (session('last_booking_ref') === $booking->booking_reference) {
            return;
        }

        abort(403, 'Unauthorized access to this reservation.');
    }

    /** GET /my-bookings — User's booking history */
    public function myBookings()
    {
        $userId = auth()->id();
        if (!$userId) {
            return redirect()->route('login')->with('info', 'Please sign in to view your reservations.');
        }

        $bookings = Booking::where('user_id', $userId)
            ->with(['property:id,name,city,primary_image,star_rating', 'room:id,name'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pages.my-bookings', compact('bookings'));
    }

    /** POST /my-bookings/{reference}/cancel — Self-service booking cancellation */
    public function cancelBooking(Request $request, string $reference, InventoryService $inventoryService)
    {
        $userId = auth()->id();
        $query = Booking::where('booking_reference', $reference);
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $request->validate(['guest_email' => 'required|email']);
            $query->where('guest_email', $request->guest_email);
        }

        $booking = $query->firstOrFail();

        if (in_array($booking->status, ['cancelled', 'completed', 'refunded'])) {
            return back()->with('error', 'This reservation cannot be cancelled in its current state.');
        }

        $booking->update([
            'status'           => 'cancelled',
            'booking_status'   => 'cancelled',
            'special_requests' => ($booking->special_requests ? $booking->special_requests . ' | ' : '') . 'Cancelled by guest on ' . now()->toFormattedDateString(),
        ]);

        // Release room inventory
        $inventoryService->releaseInventory($booking);

        return back()->with('success', "Booking #{$reference} has been cancelled successfully. Your room dates have been released.");
    }
}
