<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$vendor = User::where('role', 'vendor')->first() ?? User::find(2);
Auth::login($vendor);

$vendorCtrl = app(\App\Http\Controllers\Vendor\VendorController::class);
$req = Request::create('/vendor/properties', 'GET');

try {
    $res = $vendorCtrl->propertyIndex($req);
    $html = $res->render();
    echo "SUCCESS: Blade rendered successfully! Length: " . strlen($html) . " bytes\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
