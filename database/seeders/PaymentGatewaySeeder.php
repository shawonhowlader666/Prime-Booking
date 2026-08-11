<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            [
                'gateway_code' => 'bkash',
                'name'         => 'bKash Merchant Payment (Direct Checkout / Tokenized)',
                'is_active'    => true,
                'is_sandbox'   => true,
                'merchant_id'  => '01770000000',
                'api_key'      => 'sandbox_bkash_app_key_84920',
                'api_secret'   => 'sandbox_bkash_app_secret_99411',
                'settings'     => ['username' => 'sandbox_user', 'password' => 'sandbox_pass'],
            ],
            [
                'gateway_code' => 'nagad',
                'name'         => 'Nagad Direct Payment Gateway',
                'is_active'    => true,
                'is_sandbox'   => true,
                'merchant_id'  => '68112009',
                'api_key'      => 'sandbox_nagad_public_key',
                'api_secret'   => 'sandbox_nagad_private_key',
                'settings'     => ['account_type' => 'merchant'],
            ],
            [
                'gateway_code' => 'sslcommerz',
                'name'         => 'SSLCommerz (VISA / Mastercard / Mobile Banking)',
                'is_active'    => true,
                'is_sandbox'   => true,
                'merchant_id'  => 'primeaviationlive',
                'api_key'      => 'sslcommerz_store_id_test',
                'api_secret'   => 'sslcommerz_store_pass_test',
                'settings'     => ['currency' => 'BDT'],
            ],
            [
                'gateway_code' => 'stripe',
                'name'         => 'Stripe International Credit / Debit Cards',
                'is_active'    => true,
                'is_sandbox'   => true,
                'merchant_id'  => 'acct_1StripeTestPartner',
                'api_key'      => 'pk_test_51StripePublicKeySample12345',
                'api_secret'   => 'sk_test_51StripeSecretKeySample12345',
                'settings'     => ['currency' => 'USD'],
            ],
        ];

        foreach ($gateways as $g) {
            PaymentGateway::updateOrCreate(
                ['gateway_code' => $g['gateway_code']],
                $g
            );
        }
    }
}
