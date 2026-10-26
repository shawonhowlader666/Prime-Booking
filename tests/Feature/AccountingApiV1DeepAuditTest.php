<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AccountingLedger;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * AccountingApiV1DeepAuditTest
 * ────────────────────────────
 * Full-Control REST API v1 Deep Audit for Platform & Vendor Accounting.
 */
class AccountingApiV1DeepAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'API Audit Admin',
            'email'    => 'admin.audit@primebooking.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'status'   => 'active',
        ]);

        $this->actingAs($this->admin, 'sanctum');
    }

    public function test_api_accounts_overview_contract_and_kpis(): void
    {
        $response = $this->getJson('/api/v1/accounts/overview');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'income' => [
                        'gross_booking_value',
                        'platform_commission',
                        'total_orders',
                        'confirmed_orders',
                    ],
                    'expenses_and_costs' => [
                        'gateway_fees',
                        'customer_refunds',
                        'cancelled_value',
                    ],
                    'liabilities_and_escrow' => [
                        'vendor_payable_pool',
                        'settled_payouts',
                        'pending_payouts',
                        'escrow_holding_pool',
                    ],
                    'profitability' => [
                        'net_operating_profit',
                        'net_margin_percentage',
                    ],
                    'meta' => [
                        'currency',
                        'start_date',
                        'end_date',
                        'audited_at',
                    ],
                ],
            ]);
    }

    public function test_api_accounts_ledger_and_pnl(): void
    {
        // 1. Ledger Endpoint
        $ledgerRes = $this->getJson('/api/v1/accounts/ledger?type=all');
        $ledgerRes->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'pagination' => [
                    'current_page',
                    'last_page',
                    'total',
                    'per_page',
                ],
            ]);

        // 2. 12-Month P&L Endpoint
        $pnlRes = $this->getJson('/api/v1/accounts/pnl?year=' . date('Y'));
        $pnlRes->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'year',
                'data' => [
                    'months',
                    'revenue',
                    'commission',
                    'payouts',
                ],
            ]);
    }

    public function test_api_vendor_statement_and_matured_escrow_balances(): void
    {
        $vendor = User::create([
            'name'     => 'API Test Vendor',
            'email'    => 'api.vendor@test.com',
            'password' => bcrypt('password'),
            'role'     => 'vendor',
        ]);

        $property = Property::create([
            'vendor_id'       => $vendor->id,
            'name'            => 'API Test Hotel',
            'slug'            => 'api-test-hotel-' . uniqid(),
            'type'            => 'Hotel',
            'city'            => 'Dhaka',
            'price_per_night' => 5000,
            'status'          => 'active',
            'commission_rate' => 12.0,
        ]);

        // Create completed stay booking
        Booking::create([
            'booking_reference' => 'API-BK-1',
            'property_id'       => $property->id,
            'guest_name'        => 'API Guest',
            'guest_email'       => 'guest.api@test.com',
            'guest_phone'       => '01711111111',
            'check_in'          => now()->subDays(4)->toDateString(),
            'check_out'         => now()->subDays(2)->toDateString(),
            'guests'            => 2,
            'nights'            => 2,
            'price_per_night'   => 5000,
            'subtotal'          => 10000,
            'tax_amount'        => 750,
            'total_price'       => 10750,
            'total_amount'      => 10750,
            'currency'          => 'BDT',
            'status'            => 'confirmed',
        ]);

        $res = $this->getJson("/api/v1/accounts/vendor/statement?vendor_id={$vendor->id}");

        $res->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'vendor_id',
                'data' => [
                    'withdrawable_balance',
                    'matured_balance',
                    'escrow_upcoming_balance',
                    'gross_sales_turnover',
                    'commission_deducted',
                    'net_earnings',
                    'payouts_settled',
                    'payouts_pending',
                    'total_bookings',
                ],
            ]);

        $this->assertEquals(10750.0, (float)$res->json('data.gross_sales_turnover'));
        $this->assertGreaterThan(0, (float)$res->json('data.withdrawable_balance'));
    }

    public function test_api_record_operational_expense(): void
    {
        $response = $this->postJson('/api/v1/accounts/expense', [
            'category'       => 'sms_gateway_fee',
            'amount'         => 450.75,
            'payment_method' => 'bkash',
            'description'    => 'BulkSMSBD monthly notification credits bundle',
            'notes'          => 'Authorized by Admin',
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('accounting_ledgers', [
            'category'     => 'sms_gateway_fee',
            'type'         => 'debit',
            'gross_amount' => 450.75,
        ]);
    }

    public function test_api_audit_trial_balance_and_immutability(): void
    {
        $response = $this->getJson('/api/v1/accounts/audit-trial-balance');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'trial_balance' => [
                    'status',
                    'is_balanced',
                    'gross_collected',
                    'platform_commission',
                    'vendor_payable_pool',
                    'discrepancy_amount',
                ],
                'immutability' => [
                    'is_tamper_free',
                    'status',
                    'total_records_checked',
                ],
            ]);

        $this->assertTrue($response->json('trial_balance.is_balanced'));
        $this->assertTrue($response->json('immutability.is_tamper_free'));
    }

    public function test_api_commission_settings_get_and_post(): void
    {
        // 1. GET Commission settings
        $getRes = $this->getJson('/api/v1/accounts/admin/commission-settings');
        $getRes->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'default_commission_rate',
                    'mobile_banking_fee',
                    'card_processing_fee',
                    'vat_rate_on_commission',
                    'tds_rate_on_commission',
                    'min_payout_threshold',
                ],
            ]);

        // 2. POST Update Commission settings
        $postRes = $this->postJson('/api/v1/accounts/admin/commission-settings', [
            'default_commission_rate' => 15.0,
            'mobile_banking_fee'      => 0.018,
            'card_processing_fee'     => 0.022,
            'min_payout_threshold'    => 1000,
        ]);
        $postRes->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertEquals(15.0, (float) \App\Models\SiteSetting::get('default_commission_rate'));
    }

    public function test_api_rewards_checkout_estimator(): void
    {
        $user = User::create([
            'name'     => 'Reward Estimator User',
            'email'    => 'reward.est@test.com',
            'password' => bcrypt('password'),
            'role'     => 'user',
        ]);

        $response = $this->postJson('/api/v1/accounts/rewards/estimate', [
            'booking_amount' => 15000.0,
            'user_id'        => $user->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'booking_amount',
                    'earnable_points',
                    'user_available_points',
                    'max_redeemable_bdt',
                    'commission_rate',
                    'platform_commission',
                    'vendor_guaranteed_payout',
                    'vendor_protected',
                ],
            ]);

        $this->assertEquals(15000.0, (float)$response->json('data.booking_amount'));
        $this->assertTrue($response->json('data.vendor_protected'));
    }

    public function test_api_vendor_tax_certificate(): void
    {
        $vendor = User::create([
            'name'     => 'Tax API Hotelier',
            'email'    => 'tax.api@test.com',
            'password' => bcrypt('password'),
            'role'     => 'vendor',
        ]);

        $res = $this->getJson("/api/v1/accounts/vendor/tax-certificate?vendor_id={$vendor->id}&year=2026&month=8");

        $res->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'certificate' => [
                    'certificate_number',
                    'vendor_id',
                    'tax_year',
                    'tax_month',
                    'period_label',
                    'gross_turnover',
                    'ota_commission',
                    'tds_withheld_5pct',
                    'vat_15pct',
                    'net_disbursed',
                    'tax_compliance',
                ],
            ]);
    }

    public function test_api_yield_analytics_metrics(): void
    {
        $response = $this->getJson('/api/v1/accounts/analytics/yield');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_bookings',
                    'total_nights_sold',
                    'gross_revenue',
                    'adr_average_daily_rate',
                    'occupancy_rate_percentage',
                    'revpar_revenue_per_room',
                    'total_active_rooms',
                    'currency',
                    'performance_tier',
                ],
            ]);
    }
}
