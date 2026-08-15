<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "========================================================================\n";
echo "🔍 SYSTEM-WIDE BLADE COMPILATION & VIEW RENDERING AUDIT\n";
echo "========================================================================\n\n";

$vendor = User::where('role', 'vendor')->first() ?? User::find(2);
$admin  = User::where('role', 'admin')->first() ?? User::find(1);
$property = Property::where('vendor_id', $vendor->id)->first() ?? Property::first();

Auth::login($vendor);

$testRoutes = [
    'Vendor Properties List' => function() use ($vendor) {
        $ctrl = app(\App\Http\Controllers\Vendor\VendorController::class);
        return $ctrl->propertyIndex(Request::create('/vendor/properties', 'GET'))->render();
    },
    'Vendor Rooms List' => function() use ($property) {
        $ctrl = app(\App\Http\Controllers\Vendor\VendorRoomController::class);
        return $ctrl->index($property->id)->render();
    },
    'Vendor Rates Calendar' => function() {
        $ctrl = app(\App\Http\Controllers\Vendor\RoomAvailabilityController::class);
        return $ctrl->index(Request::create('/vendor/availability', 'GET'))->render();
    },
    'Vendor Dashboard' => function() {
        $ctrl = app(\App\Http\Controllers\Vendor\VendorDashboardController::class);
        return $ctrl->index()->render();
    },
];

$errorsFound = 0;

foreach ($testRoutes as $name => $fn) {
    try {
        $output = $fn();
        echo "✅ [PASSED] {$name} — Rendered cleanly (" . strlen($output) . " bytes)\n";
    } catch (\Throwable $e) {
        echo "❌ [FAILED] {$name} — Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
        $errorsFound++;
    }
}

// Switch to Admin
Auth::login($admin);
$adminRoutes = [
    'Admin Properties List' => function() {
        $ctrl = app(\App\Http\Controllers\Admin\PropertyManagementController::class);
        return $ctrl->index(Request::create('/admin/properties', 'GET'))->render();
    },
    'Admin Rooms List' => function() use ($property) {
        $ctrl = app(\App\Http\Controllers\Admin\RoomController::class);
        return $ctrl->index($property->id)->render();
    },
];

foreach ($adminRoutes as $name => $fn) {
    try {
        $output = $fn();
        echo "✅ [PASSED] {$name} — Rendered cleanly (" . strlen($output) . " bytes)\n";
    } catch (\Throwable $e) {
        echo "❌ [FAILED] {$name} — Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
        $errorsFound++;
    }
}

echo "\n========================================================================\n";
echo "Total Views Audited: " . (count($testRoutes) + count($adminRoutes)) . " | Errors: {$errorsFound}\n";
echo "========================================================================\n";
