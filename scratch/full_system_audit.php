<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use App\Models\Room;
use App\Models\Booking;

echo "=============================================\n";
echo "       PRIME BOOKING FULL SYSTEM AUDIT       \n";
echo "=============================================\n\n";

$errors = [];

// 1. Check Database Connectivity
try {
    DB::connection()->getPdo();
    $propCount = Property::count();
    $roomCount = Room::count();
    $bookingCount = Booking::count();
    echo "[OK] Database Connected. (Properties: {$propCount}, Rooms: {$roomCount}, Bookings: {$bookingCount})\n";
} catch (\Throwable $e) {
    $errors[] = "Database Connection Failed: " . $e->getMessage();
    echo "[FAIL] Database Connection: " . $e->getMessage() . "\n";
}

// 2. Check Route Integrity
try {
    $routes = Route::getRoutes();
    $routeCount = count($routes);
    echo "[OK] Routes Registered: {$routeCount} routes.\n";
} catch (\Throwable $e) {
    $errors[] = "Route Error: " . $e->getMessage();
    echo "[FAIL] Routes Error: " . $e->getMessage() . "\n";
}

// 3. Test Core Blade Views Compilation
$viewsToTest = [
    'pages.search-results',
    'pages.hotel-detail',
    'pages.booking-form',
    'pages.booking-confirmation',
    'pages.booking-voucher-print',
    'pages.booking-invoice-print',
    'components.layout.header',
    'components.layout.footer',
    'components.search.loading-skeleton-modal',
    'components.auth-modal',
    'layouts.main',
    'layouts.admin',
    'layouts.vendor',
];

$property = Property::with('rooms')->first();
$booking = Booking::first();

foreach ($viewsToTest as $viewName) {
    try {
        if (!View::exists($viewName)) {
            $errors[] = "View does not exist: {$viewName}";
            echo "[FAIL] View Missing: {$viewName}\n";
            continue;
        }

        // Test compilation with full mock controller data
        $data = [
            'property' => $property,
            'booking' => $booking,
            'addons' => collect(),
            'checkinCarbon' => now(),
            'checkoutCarbon' => now()->addDays(1),
            'checkIn' => now()->format('Y-m-d'),
            'checkOut' => now()->addDays(1)->format('Y-m-d'),
            'selectedRoom' => $property->rooms->first(),
            'guestStr' => '2 adults, 1 room',
            'roomsCountStr' => '1 Room',
            'activePage' => 'home',
            'properties' => collect([$property]),
            'cities' => ['Dhaka', 'Cox\'s Bazar'],
            'totalResults' => 1,
            'minPrice' => 1000,
            'maxPrice' => 50000,
            'destination' => 'Cox\'s Bazar',
            'checkin' => now()->format('Y-m-d'),
            'checkout' => now()->addDays(1)->format('Y-m-d'),
            'guests' => 2,
            'roomsCount' => 1,
            'subtotal' => 5000,
            'taxes' => 375,
            'grandTotal' => 5375,
            'nights' => 1,
            'pricePerNight' => 5000,
            'earnedPoints' => 50,
            'discountAmount' => 0,
            'searchResults' => [
                'merged_results' => collect([$property]),
                'total_count' => 1,
                'per_page' => 10,
                'current_page' => 1,
                'last_page' => 1,
            ],
            'searchType' => 'hotel',
            'guestRating' => null,
            'starRating' => null,
            'sortBy' => 'recommended',
            'amenities' => [],
            'availableCities' => ['Cox\'s Bazar', 'Dhaka'],
            'priceRange' => ['min' => 1000, 'max' => 50000],
            'filterCounts' => [],
            'popularAreas' => ['Kolatoli', 'Inani Beach'],
            'related' => collect([$property]),
            'socialProof' => ['booked_today' => 5, 'viewing_now' => 12, 'is_popular' => true],
            'seoSchema' => '',
            'reviews' => \App\Models\Review::with('user')->limit(5)->get(),
            'errors' => new \Illuminate\Support\ViewErrorBag(),
        ];

        // Authenticate mock user for admin view rendering
        if ($viewName === 'layouts.admin') {
            $adminUser = \App\Models\User::where('role', 'admin')->first() ?? new \App\Models\User(['name' => 'Admin', 'role' => 'admin', 'email' => 'admin@primebooking.com.bd']);
            auth()->setUser($adminUser);
        }

        View::make($viewName, $data)->render();
        echo "[OK] Blade View Verified: {$viewName}\n";
    } catch (\Throwable $e) {
        $errors[] = "View Error ({$viewName}): " . $e->getMessage();
        echo "[FAIL] View Error ({$viewName}): " . $e->getMessage() . "\n";
    }
}

// 4. Test Key Services
try {
    $formatted = \App\Services\CurrencyService::format(1500);
    echo "[OK] CurrencyService format verified: {$formatted}\n";
} catch (\Throwable $e) {
    $errors[] = "CurrencyService Error: " . $e->getMessage();
}

try {
    $inventoryService = app(\App\Services\InventoryService::class);
    echo "[OK] InventoryService instantiated.\n";
} catch (\Throwable $e) {
    $errors[] = "InventoryService Error: " . $e->getMessage();
}

try {
    $autoComplete = app(\App\Services\Search\AutoCompleteService::class);
    echo "[OK] AutoCompleteService instantiated.\n";
} catch (\Throwable $e) {
    $errors[] = "AutoCompleteService Error: " . $e->getMessage();
}

echo "\n=============================================\n";
if (empty($errors)) {
    echo "  🎉 AUDIT RESULT: 100% CLEAN - ZERO ERRORS! \n";
} else {
    echo "  ⚠️ AUDIT RESULT: " . count($errors) . " ERRORS FOUND! \n";
    foreach ($errors as $err) {
        echo "  - {$err}\n";
    }
}
echo "=============================================\n";
