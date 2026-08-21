<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $property = \App\Models\Property::where('status', 'active')->first() ?? \App\Models\Property::first();
    $room = $property->rooms()->first();
    
    $payload = [
        '_token' => 'dummy_csrf',
        'property_id' => $property->id,
        'room_id' => $room ? $room->id : null,
        'guest_name' => 'Shawon Howlader',
        'guest_email' => 'shawonhowlader1044@gmail.com',
        'guest_phone' => '01606352642',
        'nationality' => 'BD',
        'check_in' => now()->format('Y-m-d'), // Today's date!
        'check_out' => now()->addDay()->format('Y-m-d'), // Tomorrow
        'guests' => 2,
        'payment_method' => 'pay_at_hotel',
        'special_requests' => 'Give me some discount!',
    ];
    
    $req = \Illuminate\Http\Request::create('/book/' . $property->id, 'POST', $payload);
    $req->headers->set('Accept', 'text/html,application/xhtml+xml');
    
    $controller = app(\App\Http\Controllers\Web\BookingFlowController::class);
    $inventoryService = app(\App\Services\InventoryService::class);
    $couponService = app(\App\Services\CouponService::class);
    $notificationService = app(\App\Services\NotificationService::class);
    
    $response = $controller->store($req, $property->id, $inventoryService, $couponService, $notificationService);
    
    echo "Booking submission response status: " . $response->getStatusCode() . "\n";
    if ($response->isRedirection()) {
        echo "Redirected to: " . $response->headers->get('Location') . "\n";
        echo "Success message: " . session('success') . "\n";
        echo "Error message: " . session('error') . "\n";
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
