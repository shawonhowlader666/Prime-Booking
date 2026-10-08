<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Property;
use App\Models\Room;
use App\Models\Location;

echo "=== VERIFICATION REPORT ===\n";

$props = Property::with('rooms')->orderBy('id')->get();
echo "Total Properties: " . $props->count() . "\n";
echo "Total Rooms: " . Room::count() . "\n";
echo "Total Locations: " . Location::count() . "\n\n";

foreach ($props as $p) {
    echo "--------------------------------------------------------\n";
    echo "ID: {$p->id} | {$p->name}\n";
    echo "  Slug: {$p->slug}\n";
    echo "  Star: {$p->star_rating} Star | Rating: {$p->rating_score} ({$p->total_reviews} reviews)\n";
    echo "  Price: ৳" . number_format($p->price_per_night) . " (Reg: ৳" . number_format($p->original_price) . ")\n";
    echo "  Phone: {$p->contact_phone}\n";
    echo "  Address: {$p->address}\n";
    echo "  Primary Image: {$p->primary_image}\n";
    echo "  Gallery Images Count: " . (is_array($p->images) ? count($p->images) : 0) . "\n";
    echo "  Video URL: " . ($p->video_url ?: 'None') . "\n";
    echo "  Rooms Count: " . $p->rooms->count() . "\n";
    
    // Sample rooms
    foreach ($p->rooms as $r) {
        $imgCount = is_array($r->images) ? count($r->images) : 0;
        echo "    • [Room {$r->id}] {$r->name} | Bed: {$r->bed_type} | ৳" . number_format($r->price_per_night) . " | Photos: {$imgCount}\n";
    }
}
