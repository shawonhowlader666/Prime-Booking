<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\OAuthController;
use App\Http\Controllers\Web\BookingFlowController;
use App\Http\Controllers\Web\PropertyDetailController;
use App\Http\Controllers\Web\PaymentCallbackController;
use App\Http\Controllers\Web\WishlistController;
use App\Http\Controllers\Web\UserDashboardController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PropertyManagementController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TenantManagementController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\InquiryManagementController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\ReviewManagementController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\FeaturedDestinationController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\TourPackageController;
use App\Http\Controllers\Admin\DealController;
use App\Http\Controllers\Admin\CmsContentController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Vendor\VendorDashboardController;
use App\Http\Controllers\Vendor\VendorPromotionController;
use App\Http\Controllers\Vendor\SubscriptionController;
use App\Http\Controllers\Vendor\RoomAvailabilityController;
use App\Http\Controllers\Vendor\PayoutRequestController;
use App\Http\Controllers\Vendor\VendorPackageController;
use App\Http\Controllers\Vendor\VendorReviewController;


// Secure System Deployment & Cache Purge Route (API Prefix for Direct Proxy Routing)
Route::get('/api/deploy-sync-secret-key-9808165d', function () {
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    $gitOut = @shell_exec('cd ' . base_path() . ' && git pull origin master 2>&1');
    return response()->json([
        'success'    => true,
        'message'    => 'View cache cleared and git sync executed successfully!',
        'git_output' => $gitOut,
    ]);
});

// Dynamic SEO XML Sitemap for Googlebot & Search Engines
Route::get('/sitemap.xml', function () {
    $properties = \App\Models\Property::active()->select('slug', 'updated_at')->get();
    $packages   = \App\Models\TourPackage::where('status', 'active')->select('slug', 'updated_at')->get();
    return response()->view('sitemap', compact('properties', 'packages'))->header('Content-Type', 'text/xml');
})->name('sitemap');

// Public Front-end Routes
Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/currency/switch/{code}', [PageController::class, 'switchCurrency'])->name('currency.switch');
Route::get('/lang/switch/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'bn', 'kr', 'es', 'zh', 'ar', 'ms', 'th', 'hi', 'fr', 'de', 'ja'])) {
        session()->put('locale', $lang);
        cookie()->queue('locale', $lang, 60 * 24 * 365);
    }
    return redirect()->back();
})->name('lang.switch');
// Dynamic robots.txt (blocks admin/vendor from indexing)
Route::get('/robots.txt', function () {
    $content = "User-agent: *\nDisallow: /admin\nDisallow: /vendor\nDisallow: /api/deploy-sync-secret-key-*\nDisallow: /payment/\nCrawl-delay: 1\nSitemap: " . url('/sitemap.xml');
    return response($content, 200, ['Content-Type' => 'text/plain']);
});

Route::middleware(['throttle:120,1'])->group(function () {
    Route::get('/api/search/autocomplete', [App\Http\Controllers\Web\AutocompleteController::class, 'search'])->name('search.autocomplete');
    Route::post('/api/search/log-query', [App\Http\Controllers\Web\AutocompleteController::class, 'logSelection'])->name('search.log.selection');
});


Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/compare', [\App\Http\Controllers\Web\CompareController::class, 'index'])->name('properties.compare');
Route::get('/hotels/{id}', [PropertyDetailController::class, 'show'])->name('hotels.show');
Route::get('/hotel/{slug}', [PropertyDetailController::class, 'show'])->name('hotel.show');
Route::get('/hotels/{id}/preview', [\App\Http\Controllers\Web\PropertyPreviewController::class, 'preview'])->name('hotels.preview');
Route::get('/hotels/{id}/brochure', [PropertyDetailController::class, 'brochure'])->name('hotels.brochure');
Route::get('/property/{slug}', [PropertyDetailController::class, 'show'])->name('property.show');
Route::get('/property/{slug}/preview', [\App\Http\Controllers\Web\PropertyPreviewController::class, 'preview'])->name('property.preview');
Route::get('/property/{slug}/brochure', [PropertyDetailController::class, 'brochure'])->name('property.brochure');
Route::post('/hotels/{id}/review', [PropertyDetailController::class, 'submitReview'])->name('hotels.review.store');
Route::post('/property/{id}/review', [PropertyDetailController::class, 'submitReview'])->name('property.review.store');
Route::post('/hotels/{id}/inquire', [PropertyDetailController::class, 'submitInquiry'])->name('hotels.inquire');
Route::post('/property/{id}/inquire', [PropertyDetailController::class, 'submitInquiry'])->name('property.inquire');

// Informational & Marketing Static Routes
Route::get('/about', fn() => view('pages.about'))->name('about');
Route::get('/about-us', fn() => view('pages.about'))->name('about.us');
Route::get('/contact', fn() => view('pages.contact'))->name('contact');
Route::get('/contact-us', fn() => view('pages.contact'))->name('contact.us');
Route::get('/privacy', fn() => view('pages.privacy'))->name('privacy');
Route::get('/privacy-policy', fn() => view('pages.privacy'))->name('privacy.policy');
Route::get('/terms', fn() => view('pages.terms'))->name('terms');
Route::get('/terms-and-conditions', fn() => view('pages.terms'))->name('terms.conditions');
Route::get('/deals', [PageController::class, 'deals'])->name('deals');
Route::get('/cashback', [PageController::class, 'cashback'])->name('cashback');
Route::get('/pointsmax', [PageController::class, 'pointsmax'])->name('pointsmax');
Route::get('/vip', [PageController::class, 'vip'])->name('vip');
Route::get('/homes', fn() => view('pages.homes'))->name('homes');
Route::get('/services', fn() => view('pages.services'))->name('services');
Route::get('/subscriptions', fn() => view('pages.subscriptions'))->name('subscriptions');

Route::get('/packages', [App\Http\Controllers\Web\TourPackageController::class, 'index'])->name('packages.index');
Route::get('/tour-packages', [App\Http\Controllers\Web\TourPackageController::class, 'index'])->name('packages');
Route::get('/packages/{slug}', [App\Http\Controllers\Web\TourPackageController::class, 'show'])->name('packages.show');
Route::post('/packages/book', [App\Http\Controllers\Web\TourPackageController::class, 'book'])->name('packages.book');
Route::post('/checkout/process', [App\Http\Controllers\Web\TourPackageController::class, 'book'])->name('checkout.process');
Route::get('/packages/voucher/{reference}', [App\Http\Controllers\Web\TourPackageController::class, 'voucher'])->name('packages.voucher');

// Domestic Flight Booking Routes
Route::get('/flights', [App\Http\Controllers\Web\FlightBookingController::class, 'index'])->name('flights.index');
Route::post('/flights/book', [App\Http\Controllers\Web\FlightBookingController::class, 'book'])->name('flights.book');
Route::get('/flights/voucher/{pnr}', [App\Http\Controllers\Web\FlightBookingController::class, 'voucher'])->name('flights.voucher');

// Airport Taxi & Transfer Routes
Route::get('/transfers', [App\Http\Controllers\Web\TransferBookingController::class, 'index'])->name('transfers.index');
Route::post('/transfers/book', [App\Http\Controllers\Web\TransferBookingController::class, 'store'])->name('transfers.book');
Route::get('/transfers/voucher/{reference}', [App\Http\Controllers\Web\TransferBookingController::class, 'voucher'])->name('transfers.voucher');

// Public Guest Inquiry Form Submission Routes
Route::post('/inquiry', [InquiryController::class, 'store'])->name('inquiry.store');
Route::post('/inquiries/store', [InquiryController::class, 'store'])->name('inquiries.store');

Route::get('/forgot-password', [App\Http\Controllers\Web\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Web\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/book/{propertyId}', [BookingFlowController::class, 'showForm'])->name('booking.form');
Route::post('/book/{propertyId}', [BookingFlowController::class, 'store'])->name('booking.store');
Route::get('/booking/confirmation/{reference}', [BookingFlowController::class, 'confirmation'])->name('booking.confirmation');
Route::get('/booking/voucher/{reference}', [BookingFlowController::class, 'downloadVoucher'])->name('booking.voucher');
Route::get('/booking/voucher/{reference}/download', [BookingFlowController::class, 'downloadVoucher'])->name('booking.voucher.download');
Route::get('/booking/invoice/{reference}', [BookingFlowController::class, 'downloadInvoice'])->name('booking.invoice');
Route::get('/booking/invoice/{reference}/download', [BookingFlowController::class, 'downloadInvoice'])->name('booking.invoice.download');
Route::get('/my-bookings', [BookingFlowController::class, 'myBookings'])->name('booking.history');
Route::post('/my-bookings/{reference}/cancel', [BookingFlowController::class, 'cancelBooking'])->name('booking.cancel');
Route::post('/api/coupon/validate', [BookingFlowController::class, 'validateCouponAjax'])->name('coupon.validate');

// Legacy WooCommerce / WordPress Product URL Auto-Redirect to Prime Booking Checkout
Route::any('/product/{slug?}', function (\Illuminate\Http\Request $request, $slug = null) {
    $roomId = $request->query('room_id');
    $propertyId = $request->query('property_id');

    if (!$propertyId && $roomId) {
        $room = \App\Models\Room::find($roomId);
        if ($room) {
            $propertyId = $room->property_id;
        }
    }

    if (!$propertyId) {
        $firstProp = \App\Models\Property::first();
        $propertyId = $firstProp ? $firstProp->id : 1;
    }

    $queryParams = $request->query();
    return redirect()->route('booking.form', array_merge(['propertyId' => $propertyId], $queryParams));
})->where('slug', '.*');

// Public High-Speed VIP & Rewards Loyalty API Endpoints (Agoda Enterprise API Parity)
Route::prefix('api/v1')->name('api.v1.')->group(function () {
    Route::get('/vip/status', function (\App\Services\VIPLoyaltyService $vipService) {
        $user = auth()->user();
        $stats = $vipService->getUserTier($user);
        return response()->json([
            'status'       => 'success',
            'success'      => true,
            'is_logged_in' => (bool)$user,
            'user'         => $user ? ['id' => $user->id, 'name' => $user->name, 'email' => $user->email] : null,
            'vip'          => $stats,
        ]);
    })->name('vip.status');

    Route::get('/user/vip-status', function (\App\Services\VIPLoyaltyService $vipService) {
        $user = auth()->user();
        $stats = $vipService->getUserTier($user);
        return response()->json([
            'status'       => 'success',
            'success'      => true,
            'is_logged_in' => (bool)$user,
            'user'         => $user ? ['id' => $user->id, 'name' => $user->name, 'email' => $user->email] : null,
            'vip'          => $stats,
        ]);
    })->name('user.vip-status');

    Route::get('/vip/tiers', function () {
        return response()->json([
            'status'  => 'success',
            'success' => true,
            'tiers'   => [
                'Bronze'   => ['min_bookings' => 0, 'discount' => (float)\App\Models\SiteSetting::get('vip_bronze_discount', 0), 'perks' => ['Best price guarantee', 'Insider deals']],
                'Silver'   => ['min_bookings' => (int)\App\Models\SiteSetting::get('vip_silver_threshold', 2), 'discount' => (float)\App\Models\SiteSetting::get('vip_silver_discount', 12), 'perks' => ['VIP deals up to 12% off']],
                'Gold'     => ['min_bookings' => (int)\App\Models\SiteSetting::get('vip_gold_threshold', 5), 'min_spend' => (float)\App\Models\SiteSetting::get('vip_gold_spend', 200), 'discount' => (float)\App\Models\SiteSetting::get('vip_gold_discount', 18), 'perks' => ['VIP deals up to 18% off']],
                'Platinum' => ['min_bookings' => (int)\App\Models\SiteSetting::get('vip_platinum_threshold', 10), 'min_spend' => (float)\App\Models\SiteSetting::get('vip_platinum_spend', 400), 'discount' => (float)\App\Models\SiteSetting::get('vip_platinum_discount', 25), 'perks' => ['VIP deals up to 25% off', 'Free breakfast on select stays']],
                'Diamond'  => ['min_bookings' => (int)\App\Models\SiteSetting::get('vip_diamond_threshold', 15), 'min_spend' => (float)\App\Models\SiteSetting::get('vip_diamond_spend', 1500), 'discount' => (float)\App\Models\SiteSetting::get('vip_diamond_discount', 25), 'perks' => ['Priority 24/7 Support', 'VIP deals up to 25% off', 'Exclusive Perks']],
            ]
        ]);
    })->name('vip.tiers');

    Route::get('/rewards/summary', function (\App\Services\RewardPointService $rewardService) {
        $user = auth()->user();
        $summary = $rewardService->getUserRewardSummary($user);
        return response()->json([
            'status'  => 'success',
            'success' => true,
            'summary' => $summary,
        ]);
    })->name('rewards.summary');

    Route::get('/pointsmax/programs', function () {
        $user = auth()->user();
        $programs = $user && $user->pointsmax_programs ? (is_array($user->pointsmax_programs) ? $user->pointsmax_programs : json_decode($user->pointsmax_programs, true)) : session('pointsmax_programs_' . ($user?->id ?? 'guest'), []);
        return response()->json([
            'status'   => 'success',
            'success'  => true,
            'programs' => $programs,
        ]);
    })->name('pointsmax.programs');

    // ── Real-time Room Availability Check ──────────────────────────────────
    Route::get('/property/{id}/check-availability', function (\Illuminate\Http\Request $request, $id, \App\Services\InventoryService $inventoryService) {
        $property = \App\Models\Property::with('rooms')->find($id);
        if (!$property) {
            return response()->json(['success' => false, 'message' => 'Property not found'], 404);
        }

        $checkIn  = $request->query('check_in', now()->format('Y-m-d'));
        $checkOut = $request->query('check_out', now()->addDay()->format('Y-m-d'));
        $roomsReq = (int) $request->query('rooms', 1);

        $availableRooms = [];
        foreach ($property->rooms as $room) {
            $avail = $inventoryService->checkAvailability((int)$room->id, (string)$checkIn, (string)$checkOut, $roomsReq);
            $availableRooms[] = [
                'room_id'       => $room->id,
                'room_name'     => $room->name,
                'price_night'   => (float)$room->price_per_night,
                'is_available'  => $avail['is_available'],
                'min_available' => $avail['min_available'] ?? 0,
                'reason'        => $avail['reason'] ?? null,
            ];
        }

        $hasAnyAvailable = empty($availableRooms) ? true : collect($availableRooms)->contains('is_available', true);

        return response()->json([
            'success'            => true,
            'property_id'        => (int)$id,
            'check_in'           => $checkIn,
            'check_out'          => $checkOut,
            'is_available'       => $hasAnyAvailable,
            'rooms_availability' => $availableRooms,
        ]);
    })->name('property.check-availability');

    // ── Dynamic Booking Price Preview ──────────────────────────────────────
    Route::post('/booking/price-preview', function (\Illuminate\Http\Request $request) {
        $propertyId = (int) $request->input('property_id');
        $roomId     = $request->input('room_id') ? (int) $request->input('room_id') : null;
        $checkIn    = $request->input('check_in', now()->format('Y-m-d'));
        $checkOut   = $request->input('check_out', now()->addDay()->format('Y-m-d'));
        $couponCode = trim((string) $request->input('coupon_code', ''));

        $property = \App\Models\Property::find($propertyId);
        if (!$property) {
            return response()->json(['success' => false, 'message' => 'Property not found'], 404);
        }

        $pricePerNight = (float) $property->price_per_night;
        if ($roomId) {
            $room = \App\Models\Room::find($roomId);
            if ($room && $room->price_per_night > 0) {
                $pricePerNight = (float) $room->price_per_night;
            }
        }

        $start = \Carbon\Carbon::parse($checkIn);
        $end   = \Carbon\Carbon::parse($checkOut);
        $nights = max(1, (int) $start->diffInDays($end));

        $subtotal = $pricePerNight * $nights;
        $discount = 0;
        $discountMsg = '';

        if (!empty($couponCode)) {
            $coupon = \App\Models\Coupon::where('code', $couponCode)->where('is_active', 1)->first();
            if ($coupon) {
                if ($coupon->type === 'percent') {
                    $discount = ($subtotal * $coupon->value) / 100;
                } else {
                    $discount = min($subtotal, (float) $coupon->value);
                }
                $discountMsg = "Coupon {$couponCode} applied (-৳" . number_format($discount) . ")";
            }
        }

        $serviceFee = round($subtotal * 0.05); // 5% platform fee
        $total = max(0, ($subtotal - $discount) + $serviceFee);

        return response()->json([
            'success'         => true,
            'nights'          => $nights,
            'price_per_night' => $pricePerNight,
            'subtotal'        => $subtotal,
            'discount'        => $discount,
            'discount_msg'    => $discountMsg,
            'service_fee'     => $serviceFee,
            'total'           => $total,
            'formatted_total' => '৳' . number_format($total),
        ]);
    })->name('booking.price-preview');

    // ── Monthly Room Availability Calendar Feed ───────────────────────────
    Route::get('/property/{id}/availability-calendar', function (\Illuminate\Http\Request $request, $id) {
        $property = \App\Models\Property::with('rooms')->find($id);
        if (!$property) {
            return response()->json(['success' => false, 'message' => 'Property not found'], 404);
        }

        $monthStr = $request->query('month', now()->format('Y-m'));
        try {
            $startDate = \Carbon\Carbon::parse($monthStr . '-01')->startOfMonth();
            $endDate   = $startDate->copy()->endOfMonth();
        } catch (\Throwable $e) {
            $startDate = now()->startOfMonth();
            $endDate   = now()->endOfMonth();
        }

        $calendarDays = [];
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $basePrice = (float)$property->price_per_night;

        foreach ($period as $day) {
            $dayStr = $day->format('Y-m-d');
            $isPast = $day->isPast() && !$day->isToday();

            $calendarDays[$dayStr] = [
                'date'         => $dayStr,
                'day'          => (int)$day->format('j'),
                'day_name'     => $day->format('D'),
                'is_past'      => $isPast,
                'is_available' => !$isPast,
                'price'        => $basePrice,
                'formatted'    => '৳' . number_format($basePrice),
            ];
        }

        return response()->json([
            'success'     => true,
            'property_id' => (int)$id,
            'month'       => $startDate->format('F Y'),
            'calendar'    => $calendarDays,
        ]);
    })->name('property.availability-calendar');

    // ── Real-Time Hotel Price Drop Alert Subscription ──────────────────────
    Route::post('/price-alert/subscribe', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'property_id'  => 'required|integer|exists:properties,id',
            'email'        => 'required|email|max:150',
            'target_price' => 'nullable|numeric|min:0',
        ]);

        $property = \App\Models\Property::findOrFail($validated['property_id']);
        $user = auth()->user();
        $email = strtolower(trim($validated['email']));

        $alert = \App\Models\PriceAlert::updateOrCreate(
            [
                'property_id' => $property->id,
                'email'       => $email,
            ],
            [
                'user_id'                => $user?->id,
                'target_price'           => $validated['target_price'] ?? null,
                'current_price_at_alert' => (float)$property->price_per_night,
                'status'                 => 'active',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "🔔 Price alert created! We'll notify {$email} as soon as the rate for {$property->name} drops.",
            'alert'   => $alert,
        ]);
    })->name('price-alert.subscribe');

    // ── Real-Time Multi-Currency Rates & Active Symbols ───────────────────
    Route::get('/currency/rates', function () {
        return response()->json([
            'success'          => true,
            'current_currency' => \App\Helpers\CurrencyHelper::current(),
            'currencies'       => \App\Helpers\CurrencyHelper::getCurrencies(),
        ]);
    })->name('currency.rates');

    // ── Guest Review Helpful Vote API ──────────────────────────────────────
    Route::post('/reviews/{id}/vote', function (\Illuminate\Http\Request $request, $id) {
        $review = \App\Models\Review::findOrFail($id);
        $type = $request->input('type', 'helpful') === 'unhelpful' ? 'unhelpful' : 'helpful';

        $sessionKey = "voted_review_{$id}";
        if (session()->has($sessionKey)) {
            return response()->json([
                'success'         => false,
                'message'         => 'You have already voted on this review.',
                'helpful_count'   => $review->helpful_count,
                'unhelpful_count' => $review->unhelpful_count,
            ], 422);
        }

        if ($type === 'helpful') {
            $review->increment('helpful_count');
        } else {
            $review->increment('unhelpful_count');
        }

        session()->put($sessionKey, $type);

        return response()->json([
            'success'         => true,
            'message'         => 'Thank you for your feedback! 👍',
            'type'            => $type,
            'helpful_count'   => $review->fresh()->helpful_count,
            'unhelpful_count' => $review->fresh()->unhelpful_count,
        ]);
    })->name('reviews.vote');
});

// Payment Gateway Routes (bKash & SSLCommerz Sandbox/Live)
Route::get('/payment/bkash/sandbox/{reference}', [PaymentCallbackController::class, 'bkashSandboxRedirect'])->name('payment.bkash.sandbox-redirect');
Route::post('/payment/bkash/sandbox-execute/{reference}', [PaymentCallbackController::class, 'bkashSandboxExecute'])->name('payment.bkash.sandbox-execute');
Route::match(['get', 'post'], '/payment/bkash/callback', [PaymentCallbackController::class, 'bkashCallback'])->name('payment.bkash.callback');

Route::get('/payment/ssl/sandbox/{reference}', [PaymentCallbackController::class, 'sslSandboxRedirect'])->name('payment.ssl.sandbox-redirect');
Route::post('/payment/ssl/sandbox-execute/{reference}', [PaymentCallbackController::class, 'sslSandboxExecute'])->name('payment.ssl.sandbox-execute');
Route::post('/payment/ssl/success', [PaymentCallbackController::class, 'sslSuccess'])->name('payment.ssl.success');
Route::post('/payment/ssl/fail', [PaymentCallbackController::class, 'sslFail'])->name('payment.ssl.fail');
Route::post('/payment/ssl/cancel', [PaymentCallbackController::class, 'sslCancel'])->name('payment.ssl.cancel');
Route::post('/payment/ssl/ipn', [PaymentCallbackController::class, 'sslIpn'])->name('payment.ssl.ipn');

// Wishlist & Favorites Routes
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::get('/my-wishlist', [WishlistController::class, 'index'])->name('wishlist');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
// Alias for AJAX calls that embed property ID in URL (map modal wishlist)
Route::post('/wishlist/toggle/{id}', function (\Illuminate\Http\Request $request, $id) {
    $request->merge(['property_id' => $id]);
    return app()->call([app(WishlistController::class), 'toggle'], ['request' => $request]);
})->name('wishlist.toggle.id');



// Super Admin Control Panel, SaaS Tenants & Settings Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── System Cache Management ──────────────────────────────────────────
    Route::post('/system/cache-clear', function () {
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Cache::flush();
        return back()->with('success', '✅ All caches cleared successfully! (views, routes, Redis/file cache)');
    })->name('system.cache-clear');

    // Property Management
    Route::get('/properties', [PropertyManagementController::class, 'index'])->name('properties.index');
    Route::get('/properties/create', [PropertyManagementController::class, 'create'])->name('properties.create');
    Route::post('/properties/store', [PropertyManagementController::class, 'store'])->name('properties.store');
    Route::get('/properties/{id}/edit', [PropertyManagementController::class, 'edit'])->name('properties.edit');
    Route::put('/properties/{id}', [PropertyManagementController::class, 'update'])->name('properties.update');
    Route::post('/properties/{id}/status', [PropertyManagementController::class, 'toggleStatus'])->name('properties.toggle-status');
    Route::post('/properties/{id}/approve', [PropertyManagementController::class, 'approve'])->name('properties.approve');
    Route::post('/properties/{id}/reject', [PropertyManagementController::class, 'reject'])->name('properties.reject');
    Route::post('/properties/bulk-action', [PropertyManagementController::class, 'bulkAction'])->name('properties.bulk-action');
    Route::delete('/properties/{id}', [PropertyManagementController::class, 'destroy'])->name('properties.destroy');

    // Room Types Management (nested under property)
    Route::get('/properties/{propertyId}/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/properties/{propertyId}/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/properties/{propertyId}/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/properties/{propertyId}/rooms/{roomId}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/properties/{propertyId}/rooms/{roomId}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/properties/{propertyId}/rooms/{roomId}', [RoomController::class, 'destroy'])->name('rooms.destroy');

    // Booking Management
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/export/csv', [BookingController::class, 'exportCsv'])->name('bookings.export');
    Route::get('/bookings/export/pdf', [BookingController::class, 'exportPdf'])->name('bookings.export-pdf');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{id}/status', [BookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::post('/bookings/{id}/payment', [BookingController::class, 'updatePayment'])->name('bookings.update-payment');
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');

    // User Management (full CRUD)
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserManagementController::class, 'show'])->name('users.show');
    Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
    Route::post('/users/{id}/ban', [UserManagementController::class, 'ban'])->name('users.ban');
    Route::post('/users/{id}/activate', [UserManagementController::class, 'activate'])->name('users.activate');
    Route::post('/users/{id}/role', [UserManagementController::class, 'updateRole'])->name('users.update-role');
    Route::post('/users/{id}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{id}/promote-vendor', [UserManagementController::class, 'promoteVendor'])->name('users.promote-vendor');
    Route::post('/users/{id}/demote', [UserManagementController::class, 'demote'])->name('users.demote');
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/bulk-action', [UserManagementController::class, 'bulkAction'])->name('users.bulk-action');

    // ── Master Financial Accounts & General Ledger ─────────────────────
    Route::get('/accounts', [App\Http\Controllers\Admin\AccountingController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/ledger', [App\Http\Controllers\Admin\AccountingController::class, 'ledger'])->name('accounts.ledger');
    Route::get('/accounts/ledger/export', [App\Http\Controllers\Admin\AccountingController::class, 'exportLedger'])->name('accounts.ledger.export');
    Route::get('/accounts/vendor-statements', [App\Http\Controllers\Admin\AccountingController::class, 'vendorStatements'])->name('accounts.vendor-statements');
    Route::get('/accounts/vendor-statements/{vendorId}/print', [App\Http\Controllers\Admin\AccountingController::class, 'printVendorStatement'])->name('accounts.vendor-statements.print');

    // ── Promotions Manager ───────────────────────────────────────────────
    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::get('/promotions/create', [PromotionController::class, 'create'])->name('promotions.create');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
    Route::get('/promotions/{promotion}/edit', [PromotionController::class, 'edit'])->name('promotions.edit');
    Route::put('/promotions/{promotion}', [PromotionController::class, 'update'])->name('promotions.update');
    Route::post('/promotions/{promotion}/toggle', [PromotionController::class, 'toggleActive'])->name('promotions.toggle');
    Route::post('/promotions/reorder', [PromotionController::class, 'reorder'])->name('promotions.reorder');
    Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');

    // ── Featured Destinations ─────────────────────────────────────────────
    Route::get('/featured-destinations', [FeaturedDestinationController::class, 'index'])->name('featured-destinations.index');
    Route::get('/destinations', [FeaturedDestinationController::class, 'index'])->name('admin.destinations.index')->name('destinations.index');
    Route::get('/featured-destinations/create', [FeaturedDestinationController::class, 'create'])->name('featured-destinations.create');
    Route::get('/destinations/create', [FeaturedDestinationController::class, 'create'])->name('admin.destinations.create')->name('destinations.create');
    Route::post('/featured-destinations', [FeaturedDestinationController::class, 'store'])->name('featured-destinations.store');
    Route::post('/destinations', [FeaturedDestinationController::class, 'store'])->name('admin.destinations.store')->name('destinations.store');
    Route::get('/featured-destinations/{destination}/edit', [FeaturedDestinationController::class, 'edit'])->name('featured-destinations.edit');
    Route::get('/destinations/{destination}/edit', [FeaturedDestinationController::class, 'edit'])->name('admin.destinations.edit')->name('destinations.edit');
    Route::put('/featured-destinations/{destination}', [FeaturedDestinationController::class, 'update'])->name('featured-destinations.update');
    Route::put('/destinations/{destination}', [FeaturedDestinationController::class, 'update'])->name('admin.destinations.update')->name('destinations.update');
    Route::post('/featured-destinations/reorder', [FeaturedDestinationController::class, 'reorder'])->name('featured-destinations.reorder');
    Route::post('/featured-destinations/ajax-add', [FeaturedDestinationController::class, 'ajaxStore'])->name('featured-destinations.ajax-add');
    Route::delete('/featured-destinations/{destination}', [FeaturedDestinationController::class, 'destroy'])->name('featured-destinations.destroy')->name('destinations.destroy');

    // ── Platform / Site Settings ──────────────────────────────────────────
    Route::get('/site-settings', [SiteSettingsController::class, 'index'])->name('site-settings.index');
    Route::post('/site-settings', [SiteSettingsController::class, 'update'])->name('site-settings.update');
    Route::post('/site-settings/ajax', [SiteSettingsController::class, 'updateSingle'])->name('site-settings.ajax');
    Route::post('/site-settings/seed', [SiteSettingsController::class, 'seedDefaults'])->name('site-settings.seed');

    // Dynamic Content Managers (Tour Packages, Deals, CMS, Amenities)
    Route::get('/packages', [App\Http\Controllers\Admin\AdminTourPackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/create', [App\Http\Controllers\Admin\TourPackageController::class, 'create'])->name('packages.create');
    Route::post('/packages', [App\Http\Controllers\Admin\TourPackageController::class, 'store'])->name('packages.store');
    Route::get('/packages/{package}/edit', [App\Http\Controllers\Admin\TourPackageController::class, 'edit'])->name('packages.edit');
    Route::put('/packages/{package}', [App\Http\Controllers\Admin\TourPackageController::class, 'update'])->name('packages.update');
    Route::post('/packages/{id}/status', [App\Http\Controllers\Admin\AdminTourPackageController::class, 'toggleStatus'])->name('packages.toggle');
    Route::delete('/packages/{id}', [App\Http\Controllers\Admin\AdminTourPackageController::class, 'destroy'])->name('packages.destroy');
    Route::resource('deals', DealController::class);
    Route::post('/deals/{id}/toggle', [DealController::class, 'toggleStatus'])->name('deals.toggle');
    Route::resource('cms', CmsContentController::class);
    Route::resource('amenities', AmenityController::class)->only(['index', 'store', 'destroy']);

    // Marketing & Promo Coupons
    Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons/store', [CouponController::class, 'store'])->name('coupons.store');
    Route::put('/coupons/{id}', [CouponController::class, 'update'])->name('coupons.update');
    Route::post('/coupons/{id}/toggle', [CouponController::class, 'toggle'])->name('coupons.toggle');
    Route::delete('/coupons/{id}', [CouponController::class, 'destroy'])->name('coupons.destroy');

    // Financial & Vendor Payouts
    Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts.index');
    Route::post('/payouts/store', [PayoutController::class, 'store'])->name('payouts.store');
    Route::post('/payouts/{id}/status', [PayoutController::class, 'updateStatus'])->name('payouts.update-status');

    // Rewards & Loyalty Points Management
    Route::get('/rewards', [App\Http\Controllers\Admin\RewardManagementController::class, 'index'])->name('rewards.index');
    Route::post('/rewards/settings', [App\Http\Controllers\Admin\RewardManagementController::class, 'updateSettings'])->name('rewards.settings.update');
    Route::post('/rewards/{id}/approve', [App\Http\Controllers\Admin\RewardManagementController::class, 'approvePayout'])->name('rewards.approve');
    Route::post('/rewards/{id}/reject', [App\Http\Controllers\Admin\RewardManagementController::class, 'rejectPayout'])->name('rewards.reject');

    // Guest Reviews Moderation
    Route::get('/reviews', [ReviewManagementController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{id}/toggle', [ReviewManagementController::class, 'toggleStatus'])->name('reviews.toggle');
    Route::delete('/reviews/{id}', [ReviewManagementController::class, 'destroy'])->name('reviews.destroy');

    // Website CMS Content & Banners
    Route::get('/content/hero', [ContentController::class, 'hero'])->name('content.hero');
    Route::post('/content/hero', [ContentController::class, 'updateHero'])->name('content.hero.update');
    Route::post('/content/hero/slides', [ContentController::class, 'storeSlide'])->name('content.hero.slides.store');
    Route::put('/content/hero/slides/{id}', [ContentController::class, 'updateSlide'])->name('content.hero.slides.update');
    Route::post('/content/hero/slides/{id}/toggle', [ContentController::class, 'toggleSlide'])->name('content.hero.slides.toggle');
    Route::delete('/content/hero/slides/{id}', [ContentController::class, 'destroySlide'])->name('content.hero.slides.destroy');
    Route::get('/content/destinations', [ContentController::class, 'destinations'])->name('content.destinations');
    Route::post('/content/destinations', [ContentController::class, 'updateDestinations'])->name('content.destinations.update');

    // VIP Loyalty Program Management
    Route::get('/vip/settings', [\App\Http\Controllers\Admin\VIPLoyaltyController::class, 'settings'])->name('vip.settings');
    Route::post('/vip/settings', [\App\Http\Controllers\Admin\VIPLoyaltyController::class, 'updateSettings'])->name('vip.settings.update');
    Route::get('/vip/members', [\App\Http\Controllers\Admin\VIPLoyaltyController::class, 'members'])->name('vip.members');

    // Amenities Catalog
    Route::get('/amenities', [AmenityController::class, 'index'])->name('amenities.index');
    Route::post('/amenities', [AmenityController::class, 'store'])->name('amenities.store');
    Route::put('/amenities/{amenity}', [AmenityController::class, 'update'])->name('amenities.update');
    Route::delete('/amenities/{amenity}', [AmenityController::class, 'destroy'])->name('amenities.destroy');

    // Guest Inquiries & Support Messages
    Route::get('/inquiries', [InquiryManagementController::class, 'index'])->name('inquiries.index');
    Route::delete('/inquiries/{id}', [InquiryManagementController::class, 'destroy'])->name('inquiries.destroy');

    // SaaS Tenants & Partner Management
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/send-test-sms', [SettingsController::class, 'sendTestSms'])->name('settings.test-sms');
    Route::get('/tenants', [TenantManagementController::class, 'index'])->name('tenants.index');
    Route::post('/tenants', [TenantManagementController::class, 'store'])->name('tenants.store');
    Route::put('/tenants/{id}', [TenantManagementController::class, 'update'])->name('tenants.update');
    Route::post('/tenants/{id}/toggle', [TenantManagementController::class, 'toggleStatus'])->name('tenants.toggle');
    Route::delete('/tenants/{id}', [TenantManagementController::class, 'destroy'])->name('tenants.destroy');

    // Airport Transfers & Taxi Management
    Route::get('/transfers', [App\Http\Controllers\Admin\AdminTransferController::class, 'index'])->name('transfers.index');
    Route::post('/transfers', [App\Http\Controllers\Admin\AdminTransferController::class, 'store'])->name('transfers.store');
    Route::put('/transfers/{id}', [App\Http\Controllers\Admin\AdminTransferController::class, 'update'])->name('transfers.update');
    Route::post('/transfers/{id}/toggle', [App\Http\Controllers\Admin\AdminTransferController::class, 'toggleStatus'])->name('transfers.toggle');
    Route::delete('/transfers/{id}', [App\Http\Controllers\Admin\AdminTransferController::class, 'destroy'])->name('transfers.destroy');


    // OTA Hotel Data Importer Tool
    Route::get('/import-hotels', [App\Http\Controllers\Admin\HotelImportController::class, 'index'])->name('import-hotels.index');
    Route::post('/import-hotels', [App\Http\Controllers\Admin\HotelImportController::class, 'store'])->name('import-hotels.store');

    // Payment Gateways & API Vault
    Route::get('/payment-gateways', [PaymentGatewayController::class, 'index'])->name('gateways.index');
    Route::put('/payment-gateways/{id}', [PaymentGatewayController::class, 'update'])->name('gateways.update');
    Route::post('/payment-gateways/{id}/toggle', [PaymentGatewayController::class, 'toggleStatus'])->name('gateways.toggle');

    // Activity Log & Audit Trail
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity.index');
    Route::delete('/activity-log/{id}', [ActivityLogController::class, 'destroy'])->name('activity.destroy');
    Route::post('/activity-log/clear', [ActivityLogController::class, 'clear'])->name('activity.clear');

    // System Cache Management
    Route::post('/system/cache-clear', function () {
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Cache::flush();
        return back()->with('success', '✅ All caches cleared (views, routes, Redis).');
    })->name('system.cache-clear');

    // System Health & Operational Diagnostics Engine
    Route::get('/system/health', function () {
        $dbStatus = 'OK';
        $dbLatencyMs = 0;
        try {
            $t0 = microtime(true);
            \Illuminate\Support\Facades\DB::select('SELECT 1');
            $dbLatencyMs = round((microtime(true) - $t0) * 1000, 2);
        } catch (\Throwable $e) {
            $dbStatus = 'ERROR: ' . $e->getMessage();
        }

        $health = [
            'status'          => $dbStatus === 'OK' ? 'HEALTHY' : 'DEGRADED',
            'timestamp'       => now()->toIso8601String(),
            'environment'     => app()->environment(),
            'php_version'     => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database'        => [
                'connection' => config('database.default'),
                'status'     => $dbStatus,
                'latency_ms' => $dbLatencyMs,
            ],
            'cache_driver'    => config('cache.default'),
            'extensions'      => [
                'pdo_mysql' => extension_loaded('pdo_mysql'),
                'mbstring'  => extension_loaded('mbstring'),
                'curl'      => extension_loaded('curl'),
                'openssl'   => extension_loaded('openssl'),
            ],
            'metrics'         => [
                'active_properties' => \App\Models\Property::where('status', 'active')->count(),
                'total_bookings'    => \App\Models\Booking::count(),
                'total_users'       => \App\Models\User::count(),
                'unapproved_reviews'=> \App\Models\Review::where('is_approved', 0)->count(),
            ],
        ];

        return response()->json($health, 200, [], JSON_PRETTY_PRINT);
    })->name('system.health');

    // Admin Destination Banners & Media Manager
    Route::get('/destinations', [FeaturedDestinationController::class, 'index'])->name('destinations.index');
    Route::post('/destinations', [FeaturedDestinationController::class, 'store'])->name('destinations.store');
    Route::put('/destinations/{destination}', [FeaturedDestinationController::class, 'update'])->name('destinations.update');
    Route::delete('/destinations/{destination}', [FeaturedDestinationController::class, 'destroy'])->name('destinations.destroy');
});

// ============================================================
// VENDOR PORTAL — Complete A-to-Z Routes
// ============================================================
Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'role:vendor,admin'])->group(function () {

    // ── Dashboard ─────────────────────────────────────────────
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');

    // ── Bookings ───────────────────────────────────────────────
    Route::get('/bookings',            [VendorDashboardController::class, 'bookings'])->name('bookings.index');
    Route::get('/bookings/{reference}', [VendorDashboardController::class, 'bookingDetail'])->name('bookings.show');
    Route::post('/bookings/{reference}/status', [VendorDashboardController::class, 'updateBookingStatus'])->name('bookings.update-status');
    Route::post('/bookings/{id}/payment',       [VendorDashboardController::class, 'updatePaymentStatus'])->name('bookings.update-payment');

    // ── Earnings & Accounts ──────────────────────────────────────
    Route::get('/accounts',                 [App\Http\Controllers\Vendor\VendorAccountingController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/statement/print', [App\Http\Controllers\Vendor\VendorAccountingController::class, 'printStatement'])->name('accounts.statement.print');
    Route::get('/earnings',                 [VendorDashboardController::class, 'earnings'])->name('earnings');
    Route::get('/earnings/export', [App\Http\Controllers\Vendor\PayoutRequestController::class, 'exportCsv'])->name('earnings.export');

    // ── Property CRUD ──────────────────────────────────────────
    Route::get('/properties',             [VendorController::class, 'propertyIndex'])->name('properties.index');
    Route::get('/properties/create',      [VendorController::class, 'createProperty'])->name('properties.create');
    Route::get('/properties/create-new',  [VendorController::class, 'createProperty'])->name('property.create');
    Route::post('/properties',            [VendorController::class, 'storeProperty'])->name('properties.store');
    Route::get('/properties/{id}/edit',   [VendorController::class, 'editProperty'])->name('properties.edit');
    Route::put('/properties/{id}',        [VendorController::class, 'updateProperty'])->name('properties.update');
    Route::post('/properties/{id}/status',[VendorController::class, 'togglePropertyStatus'])->name('properties.toggle-status');
    Route::delete('/properties/{id}',     [VendorController::class, 'destroyProperty'])->name('properties.destroy');

    // ── Vendor Room CRUD ───────────────────────────────────────
    Route::get('/properties/{propertyId}/rooms',                      [App\Http\Controllers\Vendor\VendorRoomController::class, 'index'])->name('rooms.index');
    Route::post('/properties/{propertyId}/rooms',                     [App\Http\Controllers\Vendor\VendorRoomController::class, 'store'])->name('rooms.store');
    Route::put('/properties/{propertyId}/rooms/{roomId}',             [App\Http\Controllers\Vendor\VendorRoomController::class, 'update'])->name('rooms.update');
    Route::delete('/properties/{propertyId}/rooms/{roomId}',          [App\Http\Controllers\Vendor\VendorRoomController::class, 'destroy'])->name('rooms.destroy');
    Route::get('/properties/{propertyId}/rooms/{roomId}/availability', [RoomAvailabilityController::class, 'index'])->name('rooms.availability');

    // ── Rates & Calendar ───────────────────────────────────────
    Route::get('/availability',                  [RoomAvailabilityController::class, 'index'])->name('availability.index');
    Route::post('/availability/update-range',    [RoomAvailabilityController::class, 'updateRange'])->name('availability.update-range');
    Route::post('/availability/weekend-surge',   [RoomAvailabilityController::class, 'applyWeekendSurge'])->name('availability.weekend-surge');

    // ── Promotions ─────────────────────────────────────────────
    Route::get('/promotions',                 [VendorPromotionController::class, 'index'])->name('promotions.index');
    Route::get('/promotions/create',          [VendorPromotionController::class, 'create'])->name('promotions.create');
    Route::post('/promotions',                [VendorPromotionController::class, 'store'])->name('promotions.store');
    Route::get('/promotions/{promotion}/edit',[VendorPromotionController::class, 'edit'])->name('promotions.edit');
    Route::put('/promotions/{promotion}',     [VendorPromotionController::class, 'update'])->name('promotions.update');
    Route::delete('/promotions/{promotion}',  [VendorPromotionController::class, 'destroy'])->name('promotions.destroy');

    // ── Tour Packages ──────────────────────────────────────────
    Route::get('/packages',          [App\Http\Controllers\Vendor\VendorTourPackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/create',   [App\Http\Controllers\Vendor\VendorTourPackageController::class, 'create'])->name('packages.create');
    Route::post('/packages',         [App\Http\Controllers\Vendor\VendorTourPackageController::class, 'store'])->name('packages.store');
    Route::get('/packages/{id}/edit',[App\Http\Controllers\Vendor\VendorTourPackageController::class, 'edit'])->name('packages.edit');
    Route::put('/packages/{id}',     [App\Http\Controllers\Vendor\VendorTourPackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{id}',  [App\Http\Controllers\Vendor\VendorTourPackageController::class, 'destroy'])->name('packages.destroy');

    // ── Guest Reviews ──────────────────────────────────────────
    Route::get('/reviews',                     [VendorReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{reviewId}/reply',   [VendorReviewController::class, 'reply'])->name('reviews.reply');

    // ── Payouts ────────────────────────────────────────────────
    Route::get('/payouts',    [App\Http\Controllers\Vendor\PayoutRequestController::class, 'index'])->name('payouts.index');
    Route::post('/payouts',   [App\Http\Controllers\Vendor\PayoutRequestController::class, 'store'])->name('payouts.store');

    // ── SaaS Plans ─────────────────────────────────────────────
    Route::get('/plans',           [SubscriptionController::class, 'index'])->name('plans.index');
    Route::post('/plans/select',   [SubscriptionController::class, 'selectPlan'])->name('plans.select');

    // ── Notifications ──────────────────────────────────────────
    Route::get('/notifications',   [VendorController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{id}/read', [VendorController::class, 'markNotificationRead'])->name('notifications.read');

    // ── Financial Reports ──────────────────────────────────────
    Route::get('/reports',         [VendorController::class, 'reports'])->name('reports');

    // ── Inquiries ──────────────────────────────────────────────
    Route::get('/inquiries',       [VendorController::class, 'inquiries'])->name('inquiries');
    Route::post('/inquiries/{id}/reply', [VendorController::class, 'replyInquiry'])->name('inquiries.reply');

    // ── My Profile & Settings ──────────────────────────────────
    Route::get('/profile',         [VendorController::class, 'profile'])->name('profile');
    Route::post('/profile',        [VendorController::class, 'updateProfile'])->name('profile.update');

    // ── Support & Help ─────────────────────────────────────────
    Route::get('/support',         [VendorController::class, 'support'])->name('support');
    Route::post('/support',        [VendorController::class, 'submitSupport'])->name('support.submit');
});


// Agoda 1:1 Booking & Checkout Flow Routes
Route::get('/checkout', [BookingFlowController::class, 'index'])->name('checkout.index');
Route::post('/checkout/process', [BookingFlowController::class, 'process'])->name('checkout.process');
Route::get('/checkout/confirmation/{reference}', [BookingFlowController::class, 'confirmation'])->name('checkout.confirmation');

Route::get('/about-us', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/contact-us', [PageController::class, 'contact'])->name('contact');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('terms');
Route::get('/trips', [PageController::class, 'trips'])->name('trips');
Route::get('/my-account/bookings', [UserDashboardController::class, 'bookings'])->name('bookings');
Route::get('/my-account/user-bookings', [UserDashboardController::class, 'bookings'])->name('account.bookings');
Route::post('/my-account/bookings/{reference}/cancel', [UserDashboardController::class, 'cancelBooking'])->name('user.bookings.cancel');

// ─── Profile & Account Pages ──────────────────────────────────────────────
Route::get('/profile',             [PageController::class, 'profile'])->name('profile');
Route::post('/profile',            [PageController::class, 'updateProfile'])->name('profile.update');
Route::get('/my-account/profile',  [PageController::class, 'profile'])->name('account.profile');

// ─── Loyalty & Rewards ────────────────────────────────────────────────────
Route::get('/vip',        [PageController::class, 'vip'])->name('vip');
Route::get('/cashback',   [PageController::class, 'cashback'])->name('cashback');
Route::get('/pointsmax',  [PageController::class, 'pointsmax'])->name('pointsmax');

// ─── User Activity Pages ──────────────────────────────────────────────────
Route::get('/messages',   [App\Http\Controllers\Web\MessageController::class, 'index'])->name('messages');
Route::post('/messages',  [App\Http\Controllers\Web\MessageController::class, 'store'])->name('messages.store');
Route::get('/reviews',    [PageController::class, 'reviews'])->name('reviews');
Route::get('/deals',      [PageController::class, 'deals'])->name('deals');
Route::get('/homes',      [PageController::class, 'homes'])->name('homes');

// ─── Travel & Service Pages ───────────────────────────────────────────────
Route::get('/airport-transfer',    [PageController::class, 'transfer'])->name('transfer');
Route::post('/airport-transfer/book', [App\Http\Controllers\Web\TransferBookingController::class, 'store'])->name('transfer.book');
Route::get('/list-your-property',  [PageController::class, 'hostProperty'])->name('host.property');



// ─────────────────────────────────────────────────────────────────────────────
// OAuth Social Login (Google, Facebook, Apple) — Laravel Socialite
// ─────────────────────────────────────────────────────────────────────────────

// Step 1: Redirect user to provider OAuth page (GET or POST for One-Tap)
Route::match(['get', 'post'], '/auth/{provider}/redirect', [OAuthController::class, 'redirect'])
    ->name('auth.social.redirect')
    ->where('provider', 'google|facebook|apple');

// Step 2: Handle provider callback & auto login/register
Route::match(['get', 'post'], '/auth/{provider}/callback', [OAuthController::class, 'callback'])
    ->name('auth.social.callback')
    ->where('provider', 'google|facebook|apple');

// Demo / Preview Account Chooser Select
Route::post('/auth/social/demo-select', [OAuthController::class, 'demoSelect'])->name('auth.social.demo-select');

// Email-only fallback (auto register/login from modal)
Route::post('/auth/email', [OAuthController::class, 'handleEmail'])->name('auth.modal.email');

// Logout
Route::post('/auth/logout', [OAuthController::class, 'logout'])->name('auth.logout');

// ─────────────────────────────────────────────────────────────────────────────
// Traditional Login / Register (Dedicated Pages)
// ─────────────────────────────────────────────────────────────────────────────

Route::get('/login',    [OAuthController::class, 'showLogin'])->name('login');
Route::get('/signin',   [OAuthController::class, 'showLogin'])->name('signin');
Route::post('/login',   [OAuthController::class, 'loginWithPassword'])->name('login.post');
Route::get('/register', [OAuthController::class, 'showRegister'])->name('register');
Route::post('/register',[OAuthController::class, 'registerWithPassword'])->name('register.post');
Route::post('/logout',  [OAuthController::class, 'logout'])->name('logout');

// Admin & Vendor Dedicated Standalone Portal Login Routes
Route::get('/admin/login',  [OAuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [OAuthController::class, 'loginAdmin'])->name('admin.login.post');
Route::get('/vendor/login', [OAuthController::class, 'showVendorLogin'])->name('vendor.login');
Route::post('/vendor/login', [OAuthController::class, 'loginVendor'])->name('vendor.login.post');

    // ── Activity Log & Audit Trail ──────────────────────────────────────
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity.index');
    Route::delete('/activity-log/{id}', [ActivityLogController::class, 'destroy'])->name('activity.destroy');
    Route::post('/activity-log/clear', [ActivityLogController::class, 'clear'])->name('activity.clear');


// Form Actions
Route::post('/inquiry', [InquiryController::class, 'store'])->name('inquiry.store');

Route::get('/lang/{locale}', function ($locale) {
    session(['locale' => $locale]);
    cookie()->queue(cookie()->forever('locale', $locale));
    return redirect()->back();
})->name('lang.switch');

// ── Universal Storage Fallback & Media Streamer ──────────────────────────
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=86400, stale-while-revalidate=604800',
    ]);
})->where('path', '.*')->name('storage.serve');

// ── Payments and Subscriptions Routes (1:1 Agoda Subscription Parity) ──
Route::get('/account/subscription', [PageController::class, 'subscriptions'])->name('subscriptions');
Route::get('/subscriptions', [PageController::class, 'subscriptions'])->name('subscriptions.alias');
Route::post('/api/user/subscription/update', [PageController::class, 'updateSubscriptionSetting'])->name('api.user.subscription.update');

// ── Rewards & Cashback Routes (1:1 Agoda/Genius Rewards Parity) ──
Route::get('/rewards', [PageController::class, 'cashback'])->name('rewards');
Route::get('/account/rewards', [PageController::class, 'cashback'])->name('account.rewards');
Route::post('/rewards/payout', [PageController::class, 'submitRewardPayout'])->name('rewards.payout.submit');

// ── PointsMAX Programs Routes (1:1 Agoda PointsMAX Parity) ──
Route::get('/account/pointsmax', [PageController::class, 'pointsmax'])->name('pointsmax');
Route::post('/pointsmax/link', [PageController::class, 'linkPointsmaxProgram'])->name('pointsmax.link');
Route::delete('/pointsmax/unlink/{id}', [PageController::class, 'unlinkPointsmaxProgram'])->name('pointsmax.unlink');


