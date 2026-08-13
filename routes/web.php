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
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\FeaturedDestinationController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\TourPackageController;
use App\Http\Controllers\Admin\DealController;
use App\Http\Controllers\Admin\CmsContentController;
use App\Http\Controllers\Admin\AmenityController;
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
Route::get('/api/search/autocomplete', [App\Http\Controllers\Web\AutocompleteController::class, 'search'])->name('search.autocomplete');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/hotels/{id}', [PropertyDetailController::class, 'show'])->name('hotels.show');
Route::get('/property/{slug}', [PropertyDetailController::class, 'show'])->name('property.show');
Route::post('/hotels/{id}/review', [PropertyDetailController::class, 'submitReview'])->name('hotels.review.store');
Route::post('/property/{id}/review', [PropertyDetailController::class, 'submitReview'])->name('property.review.store');
Route::get('/packages', [App\Http\Controllers\Web\TourPackageController::class, 'index'])->name('packages.index');
Route::get('/tour-packages', [App\Http\Controllers\Web\TourPackageController::class, 'index'])->name('packages');
Route::get('/packages/{slug}', [App\Http\Controllers\Web\TourPackageController::class, 'show'])->name('packages.show');

// Domestic Flight Booking Routes
Route::get('/flights', [App\Http\Controllers\Web\FlightBookingController::class, 'index'])->name('flights.index');
Route::post('/flights/book', [App\Http\Controllers\Web\FlightBookingController::class, 'book'])->name('flights.book');
Route::get('/flights/voucher/{pnr}', [App\Http\Controllers\Web\FlightBookingController::class, 'voucher'])->name('flights.voucher');

// Airport Taxi & Transfer Routes
Route::get('/transfers', [App\Http\Controllers\Web\TransferBookingController::class, 'index'])->name('transfers.index');
Route::post('/transfers/book', [App\Http\Controllers\Web\TransferBookingController::class, 'store'])->name('transfers.book');

// Public Guest Inquiry Form Submission Routes
Route::post('/inquiry', [InquiryController::class, 'store'])->name('inquiry.store');
Route::post('/inquiries/store', [InquiryController::class, 'store'])->name('inquiries.store');

Route::get('/forgot-password', [App\Http\Controllers\Web\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Web\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/book/{propertyId}', [BookingFlowController::class, 'showForm'])->name('booking.form');
Route::post('/book/{propertyId}', [BookingFlowController::class, 'store'])->name('booking.store');
Route::get('/booking/confirmation/{reference}', [BookingFlowController::class, 'confirmation'])->name('booking.confirmation');
Route::get('/booking/voucher/{reference}', [BookingFlowController::class, 'confirmation'])->name('booking.voucher');
Route::get('/booking/voucher/{reference}/download', [BookingFlowController::class, 'downloadVoucher'])->name('booking.voucher.download');
Route::get('/my-bookings', [BookingFlowController::class, 'myBookings'])->name('booking.history');

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
    Route::post('/users/{id}/promote-vendor', [UserManagementController::class, 'promoteVendor'])->name('users.promote-vendor');
    Route::post('/users/{id}/demote', [UserManagementController::class, 'demote'])->name('users.demote');
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');

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
    Route::get('/featured-destinations/create', [FeaturedDestinationController::class, 'create'])->name('featured-destinations.create');
    Route::post('/featured-destinations', [FeaturedDestinationController::class, 'store'])->name('featured-destinations.store');
    Route::get('/featured-destinations/{destination}/edit', [FeaturedDestinationController::class, 'edit'])->name('featured-destinations.edit');
    Route::put('/featured-destinations/{destination}', [FeaturedDestinationController::class, 'update'])->name('featured-destinations.update');
    Route::post('/featured-destinations/reorder', [FeaturedDestinationController::class, 'reorder'])->name('featured-destinations.reorder');
    Route::post('/featured-destinations/ajax-add', [FeaturedDestinationController::class, 'ajaxStore'])->name('featured-destinations.ajax-add');
    Route::delete('/featured-destinations/{destination}', [FeaturedDestinationController::class, 'destroy'])->name('featured-destinations.destroy');

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
    Route::resource('cms', CmsContentController::class)->only(['index', 'edit', 'update']);
    Route::resource('amenities', AmenityController::class)->only(['index', 'store', 'destroy']);

    // Marketing & Promo Coupons
    Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons/store', [CouponController::class, 'store'])->name('coupons.store');
    Route::put('/coupons/{id}', [CouponController::class, 'update'])->name('coupons.update');
    Route::post('/coupons/{id}/toggle', [CouponController::class, 'toggle'])->name('coupons.toggle');
    Route::delete('/coupons/{id}', [CouponController::class, 'destroy'])->name('coupons.destroy');

    // Financial & Vendor Payouts
    Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts.index');
    Route::post('/payouts/{id}/status', [PayoutController::class, 'updateStatus'])->name('payouts.update-status');

    // Guest Reviews Moderation
    Route::get('/reviews', [ReviewManagementController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{id}/toggle', [ReviewManagementController::class, 'toggleStatus'])->name('reviews.toggle');
    Route::delete('/reviews/{id}', [ReviewManagementController::class, 'destroy'])->name('reviews.destroy');

    // Website CMS Content & Banners
    Route::get('/content/hero', [ContentController::class, 'hero'])->name('content.hero');
    Route::post('/content/hero', [ContentController::class, 'updateHero'])->name('content.hero.update');

    // Guest Inquiries & Support Messages
    Route::get('/inquiries', [InquiryManagementController::class, 'index'])->name('inquiries.index');
    Route::delete('/inquiries/{id}', [InquiryManagementController::class, 'destroy'])->name('inquiries.destroy');

    // SaaS Tenants & Partner Management
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
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

    // Admin Destination Banners & Media Manager
    Route::get('/destinations', [App\Http\Controllers\Admin\AdminDestinationController::class, 'index'])->name('destinations.index');
    Route::post('/destinations', [App\Http\Controllers\Admin\AdminDestinationController::class, 'store'])->name('destinations.store');
    Route::put('/destinations/{id}', [App\Http\Controllers\Admin\AdminDestinationController::class, 'update'])->name('destinations.update');
    Route::delete('/destinations/{id}', [App\Http\Controllers\Admin\AdminDestinationController::class, 'destroy'])->name('destinations.destroy');
});

// Vendor Portal Routes
Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'role:vendor,admin'])->group(function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings', [VendorDashboardController::class, 'bookings'])->name('bookings.index');
    Route::get('/bookings/{reference}', [VendorDashboardController::class, 'bookingDetail'])->name('bookings.show');
    Route::get('/earnings', [VendorDashboardController::class, 'earnings'])->name('earnings');

    // Vendor Property CRUD
    Route::get('/properties/create', [VendorController::class, 'createProperty'])->name('properties.create');
    Route::get('/properties/create-new', [VendorController::class, 'createProperty'])->name('property.create');
    Route::post('/properties', [VendorController::class, 'storeProperty'])->name('properties.store');
    Route::get('/properties/{id}/edit', [VendorController::class, 'editProperty'])->name('properties.edit');
    Route::put('/properties/{id}', [VendorController::class, 'updateProperty'])->name('properties.update');
    Route::post('/properties/{id}/status', [VendorController::class, 'togglePropertyStatus'])->name('properties.toggle-status');
    Route::delete('/properties/{id}', [VendorController::class, 'destroyProperty'])->name('properties.destroy');

    // Vendor Promotions (own properties only, requires admin approval)
    Route::get('/promotions', [VendorPromotionController::class, 'index'])->name('promotions.index');
    Route::get('/promotions/create', [VendorPromotionController::class, 'create'])->name('promotions.create');
    Route::post('/promotions', [VendorPromotionController::class, 'store'])->name('promotions.store');
    Route::get('/promotions/{promotion}/edit', [VendorPromotionController::class, 'edit'])->name('promotions.edit');
    Route::put('/promotions/{promotion}', [VendorPromotionController::class, 'update'])->name('promotions.update');
    Route::delete('/promotions/{promotion}', [VendorPromotionController::class, 'destroy'])->name('promotions.destroy');

    // SaaS Plans
    Route::get('/plans', [SubscriptionController::class, 'index'])->name('plans.index');
    Route::post('/plans/select', [SubscriptionController::class, 'selectPlan'])->name('plans.select');

    // Vendor Rates & Calendar Availability
    Route::get('/availability', [RoomAvailabilityController::class, 'index'])->name('availability.index');
    Route::post('/availability/update-range', [RoomAvailabilityController::class, 'updateRange'])->name('availability.update-range');

    // Vendor Earnings & Payout Requests
    Route::get('/payouts', [PayoutRequestController::class, 'index'])->name('payouts.index');
    Route::post('/payouts', [PayoutRequestController::class, 'store'])->name('payouts.store');
    Route::get('/earnings/export', [PayoutRequestController::class, 'exportCsv'])->name('earnings.export');

    // Vendor Tour Packages
    Route::get('/packages', [App\Http\Controllers\Vendor\VendorTourPackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/create', [App\Http\Controllers\Vendor\VendorTourPackageController::class, 'create'])->name('packages.create');
    Route::post('/packages', [App\Http\Controllers\Vendor\VendorTourPackageController::class, 'store'])->name('packages.store');
    Route::delete('/packages/{id}', [App\Http\Controllers\Vendor\VendorTourPackageController::class, 'destroy'])->name('packages.destroy');

    // Vendor Guest Reviews
    Route::get('/reviews', [VendorReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{reviewId}/reply', [VendorReviewController::class, 'reply'])->name('reviews.reply');
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

