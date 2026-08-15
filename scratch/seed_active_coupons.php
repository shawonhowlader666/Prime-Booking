<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Coupon;

$coupons = [
    [
        'code'        => 'PRIME10',
        'type'        => 'percentage',
        'amount'      => 10.00,
        'min_spend'   => 2000.00,
        'expires_at'  => date('Y-12-31'),
        'usage_limit' => 500,
        'status'      => 'active',
    ],
    [
        'code'        => 'EID2026',
        'type'        => 'percentage',
        'amount'      => 15.00,
        'min_spend'   => 5000.00,
        'expires_at'  => date('Y-12-31'),
        'usage_limit' => 200,
        'status'      => 'active',
    ],
    [
        'code'        => 'FLAT1000',
        'type'        => 'fixed',
        'amount'      => 1000.00,
        'min_spend'   => 6000.00,
        'expires_at'  => date('Y-12-31'),
        'usage_limit' => 100,
        'status'      => 'active',
    ],
];

foreach ($coupons as $c) {
    Coupon::updateOrCreate(['code' => $c['code']], $c);
    echo "✅ Coupon [{$c['code']}] is Active ({$c['amount']}" . ($c['type'] === 'percentage' ? '%' : ' BDT') . " Off)!\n";
}
