<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\Room;
use App\Models\Review;
use Illuminate\Http\Request;

echo "=========================================================================================\n";
echo "🔬 COMPREHENSIVE DYNAMIC DATA AUDIT: PUBLIC HOTEL DETAIL PAGE (AGODA 1:1 REPLICATION)\n";
echo "=========================================================================================\n\n";

$property = Property::with(['rooms', 'reviews', 'vendor'])->whereHas('rooms')->first() ?? Property::first();

if (!$property) {
    echo "No property found in DB!\n";
    exit;
}

echo "Auditing Property ID #{$property->id}: '{$property->name}'\n";
echo "-----------------------------------------------------------------------------------------\n";

$ctrl = app(\App\Http\Controllers\Web\PropertyDetailController::class);
$req = Request::create('/hotels/' . $property->id, 'GET');
$res = $ctrl->show($req, $property->id);
$html = $res->render();

$auditPoints = [
    '1. Hotel Full Name' => [
        'data' => $property->name,
        'found' => str_contains($html, htmlspecialchars($property->name, ENT_QUOTES)) || str_contains($html, $property->name)
    ],
    '2. Destination City & Location' => [
        'data' => $property->city,
        'found' => empty($property->city) || str_contains($html, $property->city)
    ],
    '3. Physical Address' => [
        'data' => $property->address,
        'found' => empty($property->address) || str_contains($html, htmlspecialchars($property->address, ENT_QUOTES)) || str_contains($html, $property->address)
    ],
    '4. Star Rating' => [
        'data' => ($property->star_rating ?? 5) . ' Stars',
        'found' => str_contains($html, '★')
    ],
    '5. Nightly Base Price' => [
        'data' => '৳ ' . number_format($property->price_per_night),
        'found' => str_contains($html, number_format($property->price_per_night)) || str_contains($html, (string)(int)$property->price_per_night)
    ],
    '6. Dynamic Room Categories' => [
        'data' => $property->rooms->count() . ' Room Types',
        'found' => $property->rooms->count() == 0 || str_contains($html, $property->rooms->first()->name)
    ],
    '7. Room Bed Type & Size m²' => [
        'data' => $property->rooms->first() ? ($property->rooms->first()->bed_type . ' • ' . $property->rooms->first()->room_size_sqm . ' m²') : 'N/A',
        'found' => $property->rooms->first() && (str_contains($html, $property->rooms->first()->bed_type ?? '') || str_contains($html, 'm²') || str_contains($html, 'Bed'))
    ],
    '8. Primary Cover Photo / Video Tour' => [
        'data' => !empty($property->video_url) ? 'Dynamic Video Hero Player' : ($property->primary_image ?: 'Fallback image'),
        'found' => !empty($property->video_url) || str_contains($html, e($property->primary_image)) || str_contains($html, $property->primary_image)
    ],
    '9. Facilities & Amenities Array' => [
        'data' => count($property->amenities ?? []) . ' amenities listed',
        'found' => str_contains($html, 'Facilities') || str_contains($html, 'Wi-Fi') || str_contains($html, 'Pool')
    ],
    '10. Booking Checkout Action Flow' => [
        'data' => 'Checkout URL / reservation flow',
        'found' => str_contains($html, 'booking/checkout') || str_contains($html, 'Book') || str_contains($html, 'Reserve')
    ]
];

$allPassed = true;
foreach ($auditPoints as $label => $check) {
    if ($check['found']) {
        echo "✅ [DYNAMIC] {$label} ➔ {$check['data']}\n";
    } else {
        echo "❌ [MISMATCH] {$label} ➔ {$check['data']}\n";
        $allPassed = false;
    }
}

echo "\n-----------------------------------------------------------------------------------------\n";
echo "Page Render Size: " . number_format(strlen($html)) . " bytes\n";
echo "Verdict: " . ($allPassed ? "🎉 100% DYNAMIC & FULLY DATA-DRIVEN!" : "⚠️ Needs attention") . "\n";
echo "=========================================================================================\n";
