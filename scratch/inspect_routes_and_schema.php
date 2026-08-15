<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;

echo "Rooms columns: " . implode(", ", Schema::getColumnListing('rooms')) . "\n";
echo "Room availabilities columns: " . implode(", ", Schema::getColumnListing('room_availabilities')) . "\n";

echo "Availability routes:\n";
foreach (Route::getRoutes() as $r) {
    if (str_contains($r->getName() ?? '', 'avail') || str_contains($r->getName() ?? '', 'rate') || str_contains($r->getName() ?? '', 'checkout') || str_contains($r->getName() ?? '', 'booking')) {
        echo " - " . $r->getName() . " (" . $r->uri() . ")\n";
    }
}
