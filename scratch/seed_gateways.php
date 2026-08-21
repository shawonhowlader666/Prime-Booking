<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$defaults = [
    [
        'gateway_code' => 'bkash',
        'name'         => 'bKash Direct Checkout (Personal & Merchant)',
        'is_active'    => true,
        'is_sandbox'   => true,
        'merchant_id'  => '01770887733',
        'api_key'      => 'bkash_sandbox_api_key_prime',
        'api_secret'   => 'bkash_sandbox_secret_prime',
    ],
    [
        'gateway_code' => 'nagad',
        'name'         => 'Nagad Online PGW Checkout',
        'is_active'    => true,
        'is_sandbox'   => true,
        'merchant_id'  => 'NAGAD_MERCHANT_01',
        'api_key'      => 'nagad_pub_key_sandbox',
        'api_secret'   => 'nagad_sec_key_sandbox',
    ],
    [
        'gateway_code' => 'sslcommerz',
        'name'         => 'SSLCommerz (Visa, Mastercard, Amex, DBBL, City Touch)',
        'is_active'    => true,
        'is_sandbox'   => true,
        'merchant_id'  => 'primebookinglive',
        'api_key'      => 'primebookinglive_store_id',
        'api_secret'   => 'primebookinglive_store_passwd',
    ],
    [
        'gateway_code' => 'pay_at_hotel',
        'name'         => 'Pay at Property / Cash on Check-in (Agoda Guarantee)',
        'is_active'    => true,
        'is_sandbox'   => false,
        'merchant_id'  => 'CASH_DESK',
        'api_key'      => null,
        'api_secret'   => null,
    ],
];

foreach ($defaults as $item) {
    \App\Models\PaymentGateway::updateOrCreate(
        ['gateway_code' => $item['gateway_code']],
        $item
    );
}

echo "Successfully seeded " . count($defaults) . " payment gateways into DB!\n";
