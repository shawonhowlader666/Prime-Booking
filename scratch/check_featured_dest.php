<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$schema = DB::select("DESCRIBE featured_destinations");
foreach ($schema as $c) {
    echo "{$c->Field} ({$c->Type})\n";
}
echo "Count: " . DB::table('featured_destinations')->count() . "\n";
