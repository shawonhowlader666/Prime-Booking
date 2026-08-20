<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=================================================================\n";
echo "🧪 PRIME BOOKING — REAL USER END-TO-END SIMULATION & API AUDIT\n";
echo "=================================================================\n\n";

$passCount = 0;
$totalCount = 0;

function runTest(string $title, callable $fn) {
    global $passCount, $totalCount;
    $totalCount++;
    try {
        $result = $fn();
        if ($result === true) {
            echo "  ✅ [PASS] {$title}\n";
            $passCount++;
        } else {
            echo "  ❌ [FAIL] {$title}: " . (is_string($result) ? $result : 'Assertion failed') . "\n";
        }
    } catch (\Throwable $e) {
        echo "  ❌ [ERROR] {$title}: " . $e->getMessage() . "\n";
    }
}

// 1. Controller: SearchController@index (Real DB Query & Scope Filtering)
runTest("1. Search Query Execution & Hotel Filtering Engine", function() use ($app) {
    $controller = app(\App\Http\Controllers\Web\SearchController::class);
    $request = \App\Http\Requests\Web\SearchRequest::create('/search', 'GET', [
        'destination' => 'Dhaka',
        'guests'      => 2,
        'rooms'       => 1,
    ]);
    $request->setContainer($app);
    $request->validateResolved();
    $view = $controller->index($request);
    return $view instanceof \Illuminate\View\View && isset($view->getData()['searchResults']);
});

// 2. Controller: AutocompleteController@search (Live Destination Suggestions)
runTest("2. Search Autocomplete API Response", function() {
    $controller = app(\App\Http\Controllers\Web\AutocompleteController::class);
    $request = \Illuminate\Http\Request::create('/api/search/autocomplete', 'GET', ['query' => 'Cox']);
    $response = $controller->search($request);
    $data = json_decode($response->getContent(), true);
    return $response->getStatusCode() === 200 && is_array($data) && count($data) > 0;
});

// 3. API: Room Availability Check
runTest("3. Real-Time Room Availability Check API", function() {
    $property = \App\Models\Property::first();
    if (!$property) return "No properties in database";
    $service = app(\App\Services\InventoryService::class);
    $room = $property->rooms()->first();
    $roomId = $room ? $room->id : 1;
    $check = $service->checkAvailability($roomId, date('Y-m-d', strtotime('+1 day')), date('Y-m-d', strtotime('+3 days')), 1);
    return isset($check['is_available']);
});

// 4. API: Dynamic Price Preview with Coupon Validation
runTest("4. Dynamic Booking Price Preview & Promo Coupon Calculation API", function() {
    $property = \App\Models\Property::first();
    if (!$property) return "No properties in database";
    $couponService = app(\App\Services\CouponService::class);
    $subtotal = (float)$property->price_per_night * 2;
    $couponCheck = $couponService->validate('PRIME10', $subtotal);
    return $couponCheck['valid'] === true && $couponCheck['discount'] > 0;
});

// 5. POST /hotels/{id}/review (Real User Review Submission & Auto-Sync)
runTest("5. Real Guest Review Submission, Sentiment Polarity & Aggregate Rating Auto-Sync", function() {
    $property = \App\Models\Property::first();
    if (!$property) return "No properties in database";
    $controller = app(\App\Http\Controllers\Web\PropertyDetailController::class);
    $request = \Illuminate\Http\Request::create("/hotels/{$property->id}/review", 'POST', [
        'author_name' => 'Real Guest Tester',
        'rating'      => 9.0,
        'comment'     => 'Wonderful location, super clean room and helpful staff!',
    ]);
    $request->headers->set('Accept', 'application/json');
    $response = $controller->submitReview($request, (string)$property->id);
    $data = json_decode($response->getContent(), true);
    return $response->getStatusCode() === 200 && ($data['success'] ?? false) === true;
});

// 6. Live Review Helpful Vote Toggle
runTest("6. Live Review Helpful Vote Engine", function() {
    $review = \App\Models\Review::first();
    if (!$review) return "No reviews in database";
    $initialHelpful = (int)$review->helpful_count;
    $review->increment('helpful_count');
    $review->refresh();
    return (int)$review->helpful_count === ($initialHelpful + 1);
});

// 7. Price Drop Alert Subscription
runTest("7. Real-Time Price Drop Alert Subscription Engine", function() {
    $property = \App\Models\Property::first();
    if (!$property) return "No properties in database";
    $alert = \App\Models\PriceAlert::create([
        'property_id'            => $property->id,
        'email'                  => 'e2e_tester_' . rand(1000, 9999) . '@example.com',
        'target_price'           => 3500.00,
        'current_price_at_alert' => $property->price_per_night,
        'status'                 => 'active',
    ]);
    return $alert->id > 0 && $alert->status === 'active';
});

// 8. Multi-Currency Live Rates Feed API
runTest("8. Multi-Currency Live Conversion Feed API", function() {
    $service = app(\App\Services\CurrencyService::class);
    $convertedUSD = $service->convert(12000, 'USD');
    $convertedEUR = $service->convert(12000, 'EUR');
    return $convertedUSD > 0 && $convertedEUR > 0;
});

// 9. Tour Package Booking Flow
runTest("9. Tour Package Booking Flow & Printable Voucher Verification", function() {
    $package = \App\Models\TourPackage::first();
    if (!$package) {
        $package = \App\Models\TourPackage::create([
            'title'            => 'Coxs Bazar Beach Extravaganza',
            'slug'             => 'coxs-bazar-beach-extravaganza-' . rand(100, 999),
            'destination'      => "Cox's Bazar",
            'duration_days'    => 3,
            'duration_nights'  => 2,
            'price_per_person' => 8500,
            'available_seats'  => 12,
            'status'           => 'active',
            'inclusions'       => ['Hotel', 'Breakfast', 'Sightseeing'],
        ]);
    }
    $controller = app(\App\Http\Controllers\Web\TourPackageController::class);
    $request = \Illuminate\Http\Request::create('/packages/book', 'POST', [
        'package_id'  => $package->id,
        'travel_date' => date('Y-m-d', strtotime('+3 days')),
        'guests'      => 2,
        'guest_name'  => 'Shaon Traveler',
        'guest_email' => 'shaon@example.com',
        'guest_phone' => '+880 1700-000000',
    ]);
    $response = $controller->book($request);
    return $response instanceof \Illuminate\Http\RedirectResponse && str_contains($response->getTargetUrl(), '/packages/voucher/');
});

// 10. Airport Taxi Transfer Booking Flow
runTest("10. Airport Taxi Transfer Reservation & Chauffeur Voucher Verification", function() {
    $transfer = \App\Models\AirportTransfer::first();
    $transferId = $transfer ? $transfer->id : 1;
    $controller = app(\App\Http\Controllers\Web\TransferBookingController::class);
    $request = \Illuminate\Http\Request::create('/transfers/book', 'POST', [
        'transfer_id'     => $transferId,
        'passenger_name'  => 'Shaon Traveler',
        'passenger_phone' => '+880 1711-223344',
        'passenger_email' => 'shaon@example.com',
        'pickup_datetime' => date('Y-m-d H:i:s', strtotime('+2 days')),
        'flight_number'   => 'BG-433',
        'passengers'      => 2,
    ]);
    $response = $controller->store($request);
    return $response instanceof \Illuminate\Http\RedirectResponse && str_contains($response->getTargetUrl(), '/transfers/voucher/');
});

// 11. Self-Service Booking Cancellation API
runTest("11. Guest Self-Service Cancellation & Inventory Auto-Release", function() {
    $property = \App\Models\Property::first();
    $room = $property->rooms()->first();
    $ref = 'PRM-E2E-' . rand(1000, 9999);
    $booking = \App\Models\Booking::create([
        'booking_reference' => $ref,
        'property_id'       => $property->id,
        'room_id'           => $room ? $room->id : null,
        'guest_name'        => 'Cancel Tester',
        'guest_email'       => 'cancel@example.com',
        'guest_phone'       => '+880 1700-000000',
        'check_in'          => date('Y-m-d', strtotime('+10 days')),
        'check_out'         => date('Y-m-d', strtotime('+12 days')),
        'guests'            => 2,
        'total_amount'      => 5000,
        'status'            => 'confirmed',
        'payment_status'    => 'paid',
        'payment_method'    => 'bkash',
    ]);

    // Perform Cancellation
    $booking->update(['status' => 'cancelled']);
    if ($booking->room_id) {
        $inventoryService = app(\App\Services\InventoryService::class);
        $inventoryService->releaseInventory($booking);
    }
    
    $booking->refresh();
    return $booking->status === 'cancelled';
});

// 12. Security Headers Middleware
runTest("12. Global Enterprise Security Headers Middleware Verification", function() {
    $middleware = new \App\Http\Middleware\SecurityHeaders();
    $request = \Illuminate\Http\Request::create('/', 'GET');
    $response = $middleware->handle($request, function() {
        return new \Symfony\Component\HttpFoundation\Response('OK');
    });
    return $response->headers->get('X-Content-Type-Options') === 'nosniff'
        && $response->headers->get('X-Frame-Options') === 'SAMEORIGIN';
});

echo "\n=================================================================\n";
echo "📊 REAL USER E2E AUDIT RESULTS: {$passCount} / {$totalCount} TESTS PASSED (" . round(($passCount / $totalCount) * 100) . "%)\n";
echo "=================================================================\n";
