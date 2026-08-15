<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;

echo "========================================================================\n";
echo "INSPECTING ALL EXISTING PROPERTIES IN DATABASE:\n";
echo "========================================================================\n";

$props = Property::with('rooms')->get();
foreach ($props as $p) {
    echo "\n🏨 [ID #{$p->id}] {$p->name}\n";
    echo "   • Type: {$p->type} | City: {$p->city} | Rating: {$p->star_rating}★\n";
    echo "   • Price: ৳{$p->price_per_night} | Address: {$p->address}\n";
    echo "   • Amenities: " . json_encode($p->amenities) . "\n";
    echo "   • Rooms count: " . $p->rooms->count() . "\n";
    if ($p->rooms->count() > 0) {
        foreach ($p->rooms as $r) {
            echo "     - Room #{$r->id}: {$r->name} (Bed: {$r->bed_type}, Size: {$r->room_size_sqm} m², Price: ৳{$r->price_per_night})\n";
        }
    }
}
