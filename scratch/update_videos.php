<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Property;

foreach (Property::all() as $p) {
    echo "ID: {$p->id} | {$p->name} | video_url: " . ($p->video_url ?: 'NONE') . PHP_EOL;
}
