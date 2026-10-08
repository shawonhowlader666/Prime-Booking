<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$props = App\Models\Property::whereIn('id', [1,2,3,4,5])->get();
foreach ($props as $p) {
    echo "ID: {$p->id} | {$p->name} | Rooms: " . $p->rooms()->count() . " | Gallery: " . count($p->images ?? []) . " | Video: " . ($p->video_url ? 'YES' : 'NO') . "\n";
}
