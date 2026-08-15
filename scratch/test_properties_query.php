<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;

foreach (\App\Models\Room::all() as $rm) {
    echo "Room #{$rm->id} -> property_id: {$rm->property_id} ({$rm->name})\n";
}
echo "=== TESTING OPTIMIZED PROPERTY QUERIES ===\n";

// Vendor query test
$vendorId = 2;
$statsRaw = Property::where('vendor_id', $vendorId)
    ->selectRaw("
        COUNT(*) as total,
        COUNT(CASE WHEN status = 'active' THEN 1 END) as active,
        COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending
    ")->first();

echo "Vendor Stats: Total={$statsRaw->total}, Active={$statsRaw->active}, Inactive={$statsRaw->inactive}, Pending={$statsRaw->pending}\n";

$q = Property::where('vendor_id', $vendorId)
    ->select([
        'id', 'vendor_id', 'name', 'slug', 'type', 'city',
        'star_rating', 'address', 'price_per_night', 'primary_image',
        'status', 'created_at'
    ])
    ->withCount(['rooms', 'bookings']);
echo "SQL: " . $q->toSql() . "\n";
$vendorProps = $q->get();
echo "First item attributes: ";
print_r($vendorProps->first()->toArray());

foreach ($vendorProps as $p) {
    echo "Property #{$p->id}: {$p->name} | Rooms Count: " . intval($p->rooms_count) . " | Bookings Count: " . intval($p->bookings_count) . "\n";
}

echo "\n=== ADMIN QUERY TEST ===\n";
$adminStats = Property::selectRaw("
    COUNT(*) as total,
    COUNT(CASE WHEN status = 'active' THEN 1 END) as active,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
    COUNT(CASE WHEN is_featured = 1 THEN 1 END) as featured,
    COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive
")->first();

echo "Admin Stats: Total={$adminStats->total}, Active={$adminStats->active}, Pending={$adminStats->pending}, Featured={$adminStats->featured}, Inactive={$adminStats->inactive}\n";
echo "SUCCESS!\n";
