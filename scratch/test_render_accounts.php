<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = app(\App\Http\Controllers\Admin\AccountingController::class);
$req = \Illuminate\Http\Request::create('/admin/accounts', 'GET');

try {
    $res = $controller->index($req);
    echo "Index method returned view: " . $res->name() . "\n";
    $html = $res->render();
    echo "Render result length: " . strlen($html) . "\n";
    echo "✅ /admin/accounts rendered successfully!\n";
} catch (\Throwable $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . " LINE: " . $e->getLine() . "\n";
}

try {
    $ledgerRes = $controller->ledger($req);
    echo "Ledger method returned view: " . $ledgerRes->name() . "\n";
    $html = $ledgerRes->render();
    echo "Render result length: " . strlen($html) . "\n";
    echo "✅ /admin/accounts/ledger rendered successfully!\n";
} catch (\Throwable $e) {
    echo "❌ ERROR in LEDGER: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . " LINE: " . $e->getLine() . "\n";
}

try {
    $stmtRes = $controller->vendorStatements($req);
    echo "Statements method returned view: " . $stmtRes->name() . "\n";
    $html = $stmtRes->render();
    echo "Render result length: " . strlen($html) . "\n";
    echo "✅ /admin/accounts/vendor-statements rendered successfully!\n";
} catch (\Throwable $e) {
    echo "❌ ERROR in STATEMENTS: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . " LINE: " . $e->getLine() . "\n";
}
