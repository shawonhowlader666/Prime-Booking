<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $vendorUser = \App\Models\User::where('role', 'vendor')->first() ?? \App\Models\User::first();
    \Illuminate\Support\Facades\Auth::setUser($vendorUser);
    
    $propertyIds = \App\Models\Property::where('vendor_id', $vendorUser->id)->pluck('id');
    $bookings = \App\Models\Booking::whereIn('property_id', $propertyIds)->paginate(20);
    
    $html = view('vendor.bookings', compact('bookings'))->render();
    echo "Vendor bookings view rendered OK! Length: " . strlen($html) . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
