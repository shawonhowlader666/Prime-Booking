<?php
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
    $app = require __DIR__ . '/bootstrap/app.php';
} else {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require __DIR__ . '/../bootstrap/app.php';
}
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Property;

foreach (Property::all() as $p) {
    echo "ID: {$p->id} | {$p->name} | City: {$p->city} | Status: {$p->status} | Img: " . ($p->primary_image ?: 'NONE') . " | Video: " . ($p->video_url ?: 'NONE') . PHP_EOL;
}
