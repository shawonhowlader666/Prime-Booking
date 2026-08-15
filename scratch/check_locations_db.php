<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Location;
use App\Models\Property;

echo "=== LOCATIONS IN DB ===" . PHP_EOL;
$locs = Location::all();
echo "Total locations: " . $locs->count() . PHP_EOL;
foreach ($locs as $l) {
    echo "ID: {$l->id} | Name: {$l->name} | City: {$l->city} | Lat: {$l->latitude} | Lng: {$l->longitude}" . PHP_EOL;
}

echo PHP_EOL . "=== DISTINCT CITIES IN PROPERTIES ===" . PHP_EOL;
$cities = Property::distinct()->pluck('city');
foreach ($cities as $c) {
    echo "- " . $c . PHP_EOL;
}
