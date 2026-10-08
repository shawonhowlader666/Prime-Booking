<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$locs = App\Models\Location::all();
echo "Locations count: " . $locs->count() . "\n";
foreach ($locs as $l) {
    echo "ID: {$l->id} | Name: {$l->name} | City: {$l->city}\n";
}

$destinations = \DB::table('destinations')->get();
echo "\nDestinations count: " . $destinations->count() . "\n";
foreach ($destinations as $d) {
    echo "ID: {$d->id} | Name: {$d->name}\n";
}
