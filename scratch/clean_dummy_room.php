<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\Room::where('name', 'like', 'Presidential Penthouse Panoramic Suite%')->delete();
echo "Cleaned dummy test room successfully!\n";
