<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$properties = App\Models\Property::active()->where('city', "Cox's Bazar")->with('rooms')->get();
echo "Active Cox's Bazar Properties: " . $properties->count() . "\n";
foreach ($properties as $p) {
    echo "• ID {$p->id}: {$p->name} (৳" . number_format($p->price_per_night) . "/night) — " . $p->rooms->count() . " rooms\n";
}
