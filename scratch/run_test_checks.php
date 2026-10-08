<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Property;
use App\Models\Room;

echo "=== SENIOR QUALITY AUDIT: 100% CHECKLIST ===\n\n";

$pass = 0;
$fail = 0;

function assertCheck($cond, $desc) {
    global $pass, $fail;
    if ($cond) {
        echo "  [PASS] $desc\n";
        $pass++;
    } else {
        echo "  [FAIL] $desc\n";
        $fail++;
    }
}

// Check 1: Count
$hotels = Property::where('city', "Cox's Bazar")->with('rooms')->get();
assertCheck($hotels->count() === 11, "Exactly 11 Cox's Bazar hotels in database (Found: {$hotels->count()})");

// Check 2: Total properties in DB
$allProps = Property::count();
assertCheck($allProps === 11, "Database contains ONLY the 11 authentic hotels (Total DB rows: {$allProps})");

// Check 3: Room counts
$allRooms = Room::count();
assertCheck($allRooms >= 80, "Total rooms populated across hotels (Found: {$allRooms} rooms)");

// Check 4: Images & Media Integrity
$missingPhotos = 0;
$missingAddresses = 0;
$missingPhones = 0;
$missingMaps = 0;
foreach ($hotels as $h) {
    if (empty($h->primary_image) || !file_exists(public_path(ltrim($h->primary_image, '/')))) {
        $missingPhotos++;
    }
    if (empty($h->address)) $missingAddresses++;
    if (empty($h->contact_phone)) $missingPhones++;
    if (empty($h->map_embed_url)) $missingMaps++;
}
assertCheck($missingPhotos === 0, "All 11 hotels have valid, existing primary cover images on disk");
assertCheck($missingAddresses === 0, "All 11 hotels have physical addresses");
assertCheck($missingPhones === 0, "All 11 hotels have reservation phone numbers");
assertCheck($missingMaps === 0, "All 11 hotels have Google Maps links");

// Check 5: Room photos
$roomsWithZeroPhotos = 0;
foreach (Room::all() as $r) {
    if (empty($r->images) || count($r->images) === 0) {
        $roomsWithZeroPhotos++;
    }
}
assertCheck($roomsWithZeroPhotos === 0, "All 82 rooms have dedicated room images assigned");

// Check 6: Prices validity
$zeroPriceRooms = Room::where('price_per_night', '<=', 0)->count();
assertCheck($zeroPriceRooms === 0, "All rooms have valid non-zero nightly rates");

echo "\nSummary: {$pass} PASSED, {$fail} FAILED\n";
