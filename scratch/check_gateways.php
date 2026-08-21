<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$gateways = \App\Models\PaymentGateway::all();
echo "Total Gateways in DB: " . $gateways->count() . "\n";
foreach ($gateways as $g) {
    echo "- ID: {$g->id} | Slug/Code: {$g->code} | Name: {$g->name} | Active: " . ($g->is_active ? 'YES' : 'NO') . " | Sandbox: " . ($g->is_sandbox ? 'YES' : 'NO') . "\n";
}
