<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$cxbProps = App\Models\Property::where('city', 'like', '%Cox%')->get();
echo "Cox's Bazar properties count: " . $cxbProps->count() . "\n";
foreach ($cxbProps as $p) {
    $bookingCount = App\Models\Booking::where('property_id', $p->id)->count();
    $roomCount = $p->rooms()->count();
    echo "ID: {$p->id} | {$p->name} | Rooms: {$roomCount} | Bookings: {$bookingCount}\n";
}
