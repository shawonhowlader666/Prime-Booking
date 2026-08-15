<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$vendor = User::where('role', 'vendor')->first() ?: User::first();
Auth::login($vendor);

try {
    $ctrl = app(\App\Http\Controllers\Vendor\RoomAvailabilityController::class);
    $view = $ctrl->index(request());
    $html = $view->render();
    echo "SUCCESS! Rendered " . strlen($html) . " bytes." . PHP_EOL;
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}
