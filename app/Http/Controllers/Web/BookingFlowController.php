<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Property;
use App\Models\Room;
use App\Models\SiteSetting;
use App\Services\Booking\BookingLifecycleService;
use App\Services\Booking\BookingPriceCalculator;
use App\Services\CouponService;
use App\Services\InventoryService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Guest checkout: quote -> hold -> book -> pay -> confirmation.
 *
 * Prices are always computed by BookingPriceCalculator on the server from the
 * room, dates and codes submitted. Amounts sent by the browser are ignored.
 */
class BookingFlowController extends Controller
{
    private const HOLD_MINUTES = 15;

    public function __construct(
        private readonly BookingPriceCalculator $calculator,
        private readonly InventoryService $inventory,
        private readonly BookingLifecycleService $lifecycle,
    ) {}

    /** GET /checkout?property_id=1&room_id=... */
    public function index(Request $request)
    {
        $propertyId = (int) $request->input('property_id');
        abort_if($propertyId <= 0, 404);

        return $this->showForm($request, $propertyId);
    }

    /** GET /book/{propertyId}?room_id=&check_in=&check_out=&guests=&rooms= */
    public function showForm(Request $request, $propertyId)
    {
        $property = Property::active()->with(['rooms' => fn ($q) => $q->where('status', 'active')->orderBy('price_per_night')])
            ->find((int) $propertyId);

        abort_if(!$property, 404, 'Property not found.');

        $hotelUrl = route('hotels.show', $property->id);

        if ($property->rooms->isEmpty()) {
            return redirect($hotelUrl)->with('error', 'This property has no rooms on sale right now.');
        }

        $room = $request->filled('room_id')
            ? $property->rooms->firstWhere('id', (int) $request->input('room_id'))
            : $property->rooms->first();

        if (!$room) {
            return redirect($hotelUrl)->with('error', 'That room is no longer available. Please choose another room.');
        }

        $room->setRelation('property', $property);

        [$checkIn, $checkOut] = $this->resolveStayDates($request->input('check_in'), $request->input('check_out'));

        $rooms  = min(BookingPriceCalculator::MAX_ROOMS_PER_BOOKING, max(1, (int) $request->input('rooms', 1)));
        $guests = min(40, max(1, (int) $request->input('guests', $request->input('adults', 2))));
        $user   = $request->user();

        // ── Checkout hold: keeps these rooms for this session while the guest types ──
        $holdToken = (string) ($request->session()->get('checkout_hold_token') ?: Str::uuid());
        $request->session()->put('checkout_hold_token', $holdToken);

        if (!$this->inventory->holdRoom((int) $room->id, $checkIn, $checkOut, $holdToken, self::HOLD_MINUTES, $rooms)) {
            $check = $this->inventory->checkAvailability((int) $room->id, $checkIn, $checkOut, $rooms, $holdToken);
            return redirect($hotelUrl . '?' . http_build_query(['check_in' => $checkIn, 'check_out' => $checkOut]))
                ->with('error', 'Sorry! ' . ($check['reason'] ?? 'This room is not available for the selected dates.'));
        }

        $couponCode = trim((string) $request->input('promo', $request->input('coupon', $request->session()->get('active_promo_code', ''))));
        if ($couponCode === '' && ($request->session()->get('prime_rewards_active') || $request->cookie('prime_rewards_active'))) {
            $couponCode = 'PRIMECASH8';
        }

        $quote = $this->calculator->quote($property, $room, $checkIn, $checkOut, $rooms, $guests, $couponCode ?: null, $user);
        if (!$quote['coupon']['valid']) {
            $couponCode = '';
        }

        $idempotencyToken = Str::random(40);

        return view('pages.booking-checkout', [
            'property'          => $property,
            'room'              => $room,
            'checkIn'           => $checkIn,
            'checkOut'          => $checkOut,
            'rooms'             => $rooms,
            'guests'            => $guests,
            'quote'             => $quote,
            'couponCode'        => $couponCode,
            'addonCatalog'      => $this->calculator->availableAddons($guests, $quote['nights']),
            'user'              => $user,
            'holdToken'         => $holdToken,
            'holdSeconds'       => $this->inventory->holdSecondsRemaining($holdToken),
            'idempotencyToken'  => $idempotencyToken,
            'paymentMethods'    => $this->enabledPaymentMethods(),
            'maxGuests'         => $this->roomGuestLimit($room, $rooms),
        ]);
    }

    /** POST /api/checkout/quote — live price for the checkout page (coupon, points, add-ons). */
    public function quote(Request $request)
    {
        $data = $request->validate([
            'property_id'   => 'required|integer',
            'room_id'       => 'required|integer',
            'check_in'      => 'required|date_format:Y-m-d|after_or_equal:today',
            'check_out'     => 'required|date_format:Y-m-d|after:check_in',
            'rooms'         => 'nullable|integer|min:1|max:' . BookingPriceCalculator::MAX_ROOMS_PER_BOOKING,
            'guests'        => 'nullable|integer|min:1|max:40',
            'coupon_code'   => 'nullable|string|max:50',
            'addons'        => 'nullable|array|max:10',
            'addons.*'      => 'string|max:40',
            'redeem_points' => 'nullable|integer|min:0|max:10000000',
        ]);

        $property = Property::active()->findOrFail($data['property_id']);
        $room     = Room::where('property_id', $property->id)->where('status', 'active')->findOrFail($data['room_id']);

        $quote = $this->calculator->quote(
            $property,
            $room,
            $data['check_in'],
            $data['check_out'],
            (int) ($data['rooms'] ?? 1),
            (int) ($data['guests'] ?? 1),
            $data['coupon_code'] ?? null,
            $request->user(),
            $data['addons'] ?? [],
            (int) ($data['redeem_points'] ?? 0),
        );

        return response()->json(['success' => true, 'quote' => $this->publicQuote($quote)]);
    }

    /** POST /api/coupon/validate */
    public function validateCouponAjax(Request $request, CouponService $couponService)
    {
        $request->validate([
            'code'        => 'required|string|max:50',
            'subtotal'    => 'required|numeric|min:0',
            'property_id' => 'nullable|integer',
        ]);

        return response()->json($couponService->validateCoupon(
            (string) $request->code,
            (float) $request->subtotal,
            $request->filled('property_id') ? (int) $request->property_id : null,
            $request->user()?->id
        ));
    }

    /** POST /checkout/process */
    public function process(Request $request)
    {
        $propertyId = (int) $request->input('property_id');
        abort_if($propertyId <= 0, 404);

        return $this->store($request, $propertyId);
    }

    /** POST /book/{propertyId} — create the booking */
    public function store(Request $request, $propertyId)
    {
        $maxNights = max(1, (int) SiteSetting::get('max_booking_nights', 30)) ?: 30;

        $request->validate([
            'guest_name'        => 'required|string|min:2|max:100',
            'guest_email'       => 'required|email|max:150',
            'guest_phone'       => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-()]{7,20}$/'],
            'check_in'          => 'required|date|after_or_equal:today',
            'check_out'         => 'required|date|after:check_in',
            'guests'            => 'required|integer|min:1|max:40',
            'rooms'             => 'nullable|integer|min:1|max:' . BookingPriceCalculator::MAX_ROOMS_PER_BOOKING,
            'room_id'           => 'nullable|integer',
            'payment_method'    => 'required|string|in:bkash,nagad,rocket,card,sslcommerz,cash,pay_at_hotel,bank_transfer',
            'coupon_code'       => 'nullable|string|max:50',
            'special_requests'  => 'nullable|string|max:500',
            'addons'            => 'nullable|array|max:10',
            'addons.*'          => 'string|max:40',
            'redeem_points'     => 'nullable|integer|min:0|max:10000000',
            'idempotency_token' => 'nullable|string|max:64',
            'hold_token'        => 'nullable|string|max:64',
        ], [
            'check_in.after_or_equal' => 'Check-in date cannot be in the past. Please select today or a future date.',
            'check_out.after'         => 'Check-out date must be at least 1 night after the check-in date.',
            'guest_phone.regex'       => 'Please enter a valid phone number.',
        ]);

        $property = Property::active()->find((int) $propertyId);
        if (!$property) {
            abort(404, 'Property not found.');
        }

        $room = $request->filled('room_id')
            ? Room::where('property_id', $property->id)->find((int) $request->room_id)
            : Room::where('property_id', $property->id)->where('status', 'active')->orderBy('price_per_night')->first();

        if ($request->filled('room_id') && !$room) {
            return $this->fail($request, 'The selected room is no longer available. Please choose another room.');
        }

        $checkIn  = Carbon::parse($request->check_in)->toDateString();
        $checkOut = Carbon::parse($request->check_out)->toDateString();
        $rooms    = max(1, (int) $request->input('rooms', 1));
        $guests   = (int) $request->guests;
        $user     = $request->user();

        if (Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)) > $maxNights) {
            return $this->fail($request, "A single booking can be at most {$maxNights} nights.");
        }

        if ($room && ($limit = $this->roomGuestLimit($room, $rooms)) !== null && $guests > $limit) {
            return $this->fail($request, "This room sleeps {$limit} guest(s) for {$rooms} room(s). Please add another room or reduce guests.");
        }

        // ── Idempotency: a retried or double-clicked submit returns the first booking ──
        $idempotencyToken = (string) $request->input('idempotency_token', '');
        $idempotencyKey   = $idempotencyToken !== '' ? 'checkout_done_' . hash('sha256', $idempotencyToken) : null;
        $lockKey          = $idempotencyToken !== '' ? 'checkout_lock_' . hash('sha256', $idempotencyToken) : null;

        if ($idempotencyKey && ($existingRef = Cache::get($idempotencyKey))) {
            $existing = Booking::where('booking_reference', $existingRef)->first();
            if ($existing) {
                return $this->bookingCreatedResponse($request, $existing);
            }
        }

        if ($lockKey && !Cache::add($lockKey, 1, 30)) {
            return $this->fail($request, 'Your booking request is already being processed. Please wait a moment.', 409);
        }

        $holdToken = (string) ($request->input('hold_token') ?: $request->session()->get('checkout_hold_token', ''));

        $quote = $this->calculator->quote(
            $property,
            $room,
            $checkIn,
            $checkOut,
            $rooms,
            $guests,
            $request->input('coupon_code'),
            $user,
            (array) $request->input('addons', []),
            (int) $request->input('redeem_points', 0),
        );

        if ($request->filled('coupon_code') && !$quote['coupon']['valid']) {
            $this->unlock($lockKey);
            return $this->fail($request, $quote['coupon']['message'] ?: 'That coupon code is not valid for this booking.');
        }

        // ── Fraud screen ─────────────────────────────────────────────────
        $fraudRisk = app(\App\Services\AI\FraudDetector::class)->evaluateBooking([
            'guest_email'  => $request->guest_email,
            'check_in'     => $checkIn,
            'total_amount' => $quote['total'],
        ], $user);

        if ($fraudRisk['decision'] === 'REJECT_OR_MANUAL_REVIEW') {
            Log::warning('Booking blocked by fraud screen', ['flags' => $fraudRisk['flags'], 'ip' => $request->ip()]);
            $this->unlock($lockKey);
            return $this->fail($request, 'Unable to process your request at this time. Please contact customer support for assistance.');
        }

        $isOffline     = in_array($request->payment_method, Booking::OFFLINE_PAYMENT_METHODS, true);
        $paymentStatus = $isOffline ? 'unpaid' : 'pending';
        $bookingStatus = $isOffline ? 'confirmed' : 'pending';

        try {
            $booking = DB::transaction(function () use ($request, $property, $room, $rooms, $guests, $checkIn, $checkOut, $quote, $user, $isOffline, $paymentStatus, $bookingStatus, $holdToken) {
                if ($room) {
                    // Serialise all checkouts for this room type; availability is re-read under the lock.
                    Room::whereKey($room->id)->lockForUpdate()->first();

                    $check = $this->inventory->checkAvailability((int) $room->id, $checkIn, $checkOut, $rooms, $holdToken ?: null);
                    if (!$check['is_available']) {
                        throw new \DomainException('Sorry! ' . ($check['reason'] ?? 'The last room was just reserved by another guest.') . ' Please select another date or room.');
                    }
                }

                $booking = $this->createWithUniqueReference([
                    'property_id'            => $property->id,
                    'room_id'                => $room?->id,
                    'user_id'                => $user?->id,
                    'guest_name'             => trim($request->guest_name),
                    'guest_email'            => strtolower(trim($request->guest_email)),
                    'guest_phone'            => trim($request->guest_phone),
                    'check_in'               => $checkIn,
                    'check_out'              => $checkOut,
                    'guests'                 => $guests,
                    'rooms_count'            => $rooms,
                    'nights'                 => $quote['nights'],
                    'price_per_night'        => $quote['price_per_night'],
                    'subtotal'               => $quote['subtotal'],
                    'tax_amount'             => $quote['tax_amount'],
                    'total_price'            => $quote['total'],
                    'total_amount'           => $quote['total'],
                    'currency'               => 'BDT',
                    'payment_method'         => $request->payment_method,
                    'payment_status'         => $paymentStatus,
                    'payment_expires_at'     => $isOffline ? null : now()->addMinutes(InventoryService::PAYMENT_WINDOW_MINUTES),
                    'status'                 => $bookingStatus,
                    'booking_status'         => $bookingStatus,
                    'special_requests'       => $request->special_requests,
                    'coupon_code'            => $quote['coupon']['valid'] ? $quote['coupon']['code'] : null,
                    'discount_amount'        => $quote['coupon']['discount'],
                    'vip_discount_amount'    => $quote['vip']['discount'],
                    'points_redeemed'        => $quote['points']['redeemed'],
                    'points_discount_amount' => $quote['points']['discount'],
                    'addons_total'           => $quote['addons_total'],
                    'commission_rate'        => $quote['commission_rate'],
                    'commission_amount'      => $quote['commission_amount'],
                    'vendor_payout_amount'   => $quote['vendor_payout'],
                ]);

                foreach ($quote['addons'] as $addon) {
                    BookingAddon::create([
                        'booking_id' => $booking->id,
                        'addon_name' => $addon['name'],
                        'price'      => $addon['price'],
                        'qty'        => 1,
                    ]);
                }

                if ($quote['points']['redeemed'] > 0) {
                    app(\App\Services\RewardPointService::class)->redeemForBooking($booking, $quote['points']['redeemed']);
                }

                if ($booking->coupon_code && !app(CouponService::class)->recordUsage($booking->coupon_code)) {
                    throw new \DomainException('This coupon has just reached its usage limit. Please remove it and try again.');
                }

                if ($holdToken !== '') {
                    $this->inventory->releaseHold($holdToken);
                }

                return $booking;
            });
        } catch (\DomainException | \RuntimeException $e) {
            $this->unlock($lockKey);
            return $this->fail($request, $e->getMessage());
        } catch (\Throwable $e) {
            report($e);
            $this->unlock($lockKey);
            return $this->fail($request, 'Booking could not be finalized. Please try again.', 500);
        }

        $this->inventory->lockAndDeduct($booking);
        $request->session()->forget('checkout_hold_token');
        $request->session()->put('last_booking_ref', $booking->booking_reference);

        if ($idempotencyKey) {
            Cache::put($idempotencyKey, $booking->booking_reference, now()->addHours(2));
        }

        if ($user) {
            app(\App\Services\VIPLoyaltyService::class)->clearUserCache((int) $user->id);
        }

        // Pay-at-hotel bookings are confirmed immediately; online bookings confirm from the payment callback.
        if ($isOffline) {
            $booking->loadMissing(['property', 'room']);
            $this->lifecycle->sendConfirmation($booking);
            try {
                app(\App\Services\RewardPointService::class)->creditBookingPoints($booking);
            } catch (\Throwable $e) {
                Log::error('Booking post-processing error: ' . $e->getMessage());
            }
        }

        return $this->bookingCreatedResponse($request, $booking);
    }

    /** GET /booking/confirmation/{reference} */
    public function confirmation($reference)
    {
        $booking = Booking::where('booking_reference', $reference)
            ->with(['property', 'room'])
            ->firstOrFail();

        $this->authorizeBookingAccess($booking);

        if ($booking->isAwaitingPayment()) {
            return redirect()->route('payment.pending', $booking->booking_reference);
        }

        if (in_array($booking->status, ['cancelled', 'failed'], true) || in_array($booking->payment_status, ['cancelled', 'failed', 'expired'], true)) {
            return redirect()->route('payment.pending', ['reference' => $booking->booking_reference, 'state' => 'expired']);
        }

        $addons   = BookingAddon::where('booking_id', $booking->id)->get();
        $room     = $booking->room;
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

        if ($booking->isAwaitingPayment()) {
            return redirect()->route('payment.pending', $booking->booking_reference);
        }

        if ($booking->status !== 'confirmed' && $booking->status !== 'completed') {
            return redirect()->route('home')
                ->with('error', 'This reservation is not active. A voucher cannot be generated.');
        }

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

    /** GET /my-bookings — User's booking history */
    public function myBookings()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('info', 'Please sign in to view your reservations.');
        }

        $userId    = $user->id;
        $userEmail = strtolower(trim((string) $user->email));
        $owned     = function ($q, string $emailColumn) use ($userId, $userEmail) {
            $q->where('user_id', $userId);
            if ($userEmail !== '') {
                $q->orWhere($emailColumn, $userEmail);
            }
        };

        $bookings = Booking::where(fn ($q) => $owned($q, 'guest_email'))
            ->with(['property:id,name,city,primary_image,star_rating', 'room:id,name'])
            ->orderByDesc('created_at')
            ->paginate(10);

        $packageBookings = \App\Models\PackageBooking::where(fn ($q) => $owned($q, 'guest_email'))
            ->with('package')
            ->latest()
            ->limit(50)
            ->get();

        $transferBookings = \App\Models\TransferBooking::where(fn ($q) => $owned($q, 'passenger_email'))
            ->with('transfer')
            ->latest()
            ->limit(50)
            ->get();

        return view('pages.my-bookings', compact('bookings', 'packageBookings', 'transferBookings'));
    }

    /** POST /my-bookings/{reference}/cancel — Self-service booking cancellation */
    public function cancelBooking(Request $request, string $reference)
    {
        $booking = Booking::where('booking_reference', $reference)->with(['property', 'room'])->firstOrFail();

        // Same access rule as viewing the booking: owner, signed link, or the browser that made it.
        $this->authorizeBookingAccess($booking, allowStaff: false);

        $terms = $this->lifecycle->cancellationTerms($booking);
        if (!$terms['allowed']) {
            return back()->with('error', $terms['reason']);
        }

        $wasPaid = $booking->payment_status === 'paid';

        if (!$this->lifecycle->cancel($booking, 'Cancelled by guest')) {
            return back()->with('error', 'This reservation cannot be cancelled in its current state.');
        }

        $message = "Booking #{$reference} has been cancelled.";
        if ($wasPaid) {
            $message .= $terms['refundable']
                ? ' Your refund has been initiated and will reach your original payment method within 7-10 working days.'
                : ' This rate is non-refundable, so no refund is due under the booking terms.';
        }

        return back()->with('success', $message);
    }

    // ─── Internals ────────────────────────────────────────────────────────

    /** Ensure only the booking owner, its vendor, staff, or the guest who made it can open booking documents. */
    private function authorizeBookingAccess(Booking $booking, bool $allowStaff = true): void
    {
        $user = auth()->user();

        if ($user && $allowStaff) {
            if ($user->isAdmin() || $user->role === 'super_admin') {
                return;
            }
            if ($user->isVendor() && $booking->property?->vendor_id === $user->id) {
                return;
            }
        }

        if ($user && ($booking->user_id === $user->id
            || ($user->email && strtolower((string) $booking->guest_email) === strtolower((string) $user->email)))) {
            return;
        }

        if (request()->hasValidSignature()) {
            session(['last_booking_ref' => $booking->booking_reference]);
            return;
        }

        if (session('last_booking_ref') === $booking->booking_reference) {
            return;
        }

        // Guest lookup: reference + the e-mail or phone used on the booking.
        $email = strtolower(trim((string) request('email', request('guest_email', ''))));
        if ($email !== '' && hash_equals(strtolower(trim((string) $booking->guest_email)), $email)) {
            session(['last_booking_ref' => $booking->booking_reference]);
            return;
        }

        $phone = preg_replace('/\D/', '', (string) request('phone', ''));
        if (strlen($phone) >= 10 && hash_equals(preg_replace('/\D/', '', (string) $booking->guest_phone), $phone)) {
            session(['last_booking_ref' => $booking->booking_reference]);
            return;
        }

        abort(403, 'Unauthorized access to booking reservation.');
    }

    /** Valid stay dates from user input, falling back to tomorrow + 2 nights. */
    private function resolveStayDates(mixed $in, mixed $out): array
    {
        try {
            $checkIn = $in ? Carbon::parse((string) $in)->startOfDay() : null;
        } catch (\Throwable $e) {
            $checkIn = null;
        }
        try {
            $checkOut = $out ? Carbon::parse((string) $out)->startOfDay() : null;
        } catch (\Throwable $e) {
            $checkOut = null;
        }

        if (!$checkIn || $checkIn->lt(now()->startOfDay())) {
            $checkIn = now()->addDay()->startOfDay();
        }
        if (!$checkOut || $checkOut->lte($checkIn)) {
            $checkOut = $checkIn->copy()->addDays($out ? 1 : 2);
        }

        $maxNights = max(1, (int) SiteSetting::get('max_booking_nights', 30)) ?: 30;
        if ($checkIn->diffInDays($checkOut) > $maxNights) {
            $checkOut = $checkIn->copy()->addDays($maxNights);
        }

        return [$checkIn->toDateString(), $checkOut->toDateString()];
    }

    private function roomGuestLimit(Room $room, int $rooms): ?int
    {
        $perRoom = (int) ($room->max_guests ?? 0);
        return $perRoom > 0 ? $perRoom * max(1, $rooms) + (int) ($room->max_children ?? 0) * max(1, $rooms) : null;
    }

    /** Payment options offered on the checkout page. */
    private function enabledPaymentMethods(): array
    {
        $methods = [
            'sslcommerz' => ['label' => 'Card / Net Banking / Mobile Wallet', 'hint' => 'Visa, Mastercard, Amex, bKash, Nagad, Rocket — secured by SSLCommerz'],
        ];

        if (app(\App\Services\Payment\BkashPaymentService::class)->isConfigured() || BookingLifecycleService::simulationAllowed()) {
            $methods['bkash'] = ['label' => 'bKash', 'hint' => 'Pay from your bKash wallet'];
        }

        if ((string) SiteSetting::get('allow_pay_at_hotel', '1') !== '0') {
            $methods['pay_at_hotel'] = ['label' => 'Pay at hotel', 'hint' => 'No payment now. Pay the full amount at check-in.'];
        }

        return $methods;
    }

    private function createWithUniqueReference(array $attributes): Booking
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $reference = 'PRM-' . date('Y') . '-' . Str::upper(Str::random(8));
            if (!Booking::where('booking_reference', $reference)->exists()) {
                return Booking::create($attributes + ['booking_reference' => $reference]);
            }
        }

        throw new \RuntimeException('Could not allocate a booking reference. Please try again.');
    }

    private function paymentUrl(Booking $booking): string
    {
        return match ($booking->payment_method) {
            'bkash'  => route('payment.bkash.sandbox-redirect', $booking->booking_reference),
            'cash', 'pay_at_hotel', 'bank_transfer' => route('booking.confirmation', $booking->booking_reference),
            default  => route('payment.ssl.sandbox-redirect', $booking->booking_reference),
        };
    }

    private function bookingCreatedResponse(Request $request, Booking $booking)
    {
        $url = $this->paymentUrl($booking);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'           => true,
                'booking_reference' => $booking->booking_reference,
                'booking_id'        => $booking->id,
                'total'             => (float) $booking->total_price,
                'redirect_url'      => $url,
            ]);
        }

        $redirect = redirect()->to($url);

        return $booking->isOfflinePayment()
            ? $redirect->with('success', 'Booking reserved successfully! Reference: ' . $booking->booking_reference)
            : $redirect;
    }

    private function fail(Request $request, string $message, int $status = 422)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }

        return back()->withInput()->with('error', $message);
    }

    private function unlock(?string $lockKey): void
    {
        if ($lockKey) {
            Cache::forget($lockKey);
        }
    }

    /** The parts of a quote the browser needs. Commission and payout figures stay server-side. */
    private function publicQuote(array $quote): array
    {
        return collect($quote)->except(['commission_rate', 'commission_amount', 'vendor_payout'])->all();
    }
}
