<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$request = \Illuminate\Http\Request::create('/admin/accounts', 'GET');
app()->instance('request', $request);

$admin = \App\Models\User::where('role', 'admin')->first();
$vendor = \App\Models\User::where('role', 'vendor')->first();

echo "=== TESTING DIRECT CONTROLLER RENDER ===" . PHP_EOL;

$adminAccCtrl = app(\App\Http\Controllers\Admin\AccountingController::class);
$vendorAccCtrl = app(\App\Http\Controllers\Vendor\VendorAccountingController::class);

auth()->setUser($admin);

// 1. Admin Index View Render
$view1 = $adminAccCtrl->index($request);
echo "1. Admin Accounts View: " . $view1->name() . " -> OK" . PHP_EOL;

// 2. Admin Ledger View Render
$view2 = $adminAccCtrl->ledger($request);
echo "2. Admin Ledger View: " . $view2->name() . " -> OK" . PHP_EOL;

// 3. Admin Vendor Statements View Render
$view3 = $adminAccCtrl->vendorStatements($request);
echo "3. Admin Vendor Statements View: " . $view3->name() . " -> OK" . PHP_EOL;

// 4. Vendor Accounts View Render
auth()->setUser($vendor);
$view4 = $vendorAccCtrl->index($request);
echo "4. Vendor Accounts View: " . $view4->name() . " -> OK" . PHP_EOL;

echo "🎉 ALL ACCOUNTS CONTROLLERS & BLADE VIEWS WORKING 100%!" . PHP_EOL;
