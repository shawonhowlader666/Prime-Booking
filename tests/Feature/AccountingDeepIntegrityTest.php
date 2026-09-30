<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AccountingLedger;
use App\Models\Booking;
use App\Models\Payout;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * AccountingDeepIntegrityTest
 * ────────────────────────────
 * Comprehensive mathematical and double-entry integrity verification of AccountingService:
 * 1. Overview KPIs (Gross, Platform Commission, Vendor Payable, Gateway Fees, Escrow)
 * 2. Monthly P&L Chart Data (12-month series)
 * 3. Vendor Statements (Single-pass aggregate math, available balance)
 * 4. Single Vendor Detailed P&L
 * 5. General Ledger Idempotency & Bootstrap
 */
class AccountingDeepIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_accounting_kpis_and_gross_booking_math(): void
    {
        // 1. Create a Vendor User
        $vendor = User::create([
            'name'     => 'Grand Hotel Vendor',
            'email'    => 'vendor.grand@test.com',
            'password' => bcrypt('password'),
            'role'     => 'vendor',
        ]);

        // 2. Create Property with 15% custom commission rate
        $property = Property::create([
            'vendor_id'       => $vendor->id,
            'name'            => 'Grand Palace Hotel',
            'slug'            => 'grand-palace-hotel-' . uniqid(),
            'type'            => 'Hotel',
            'city'            => 'Dhaka',
            'address'         => 'Gulshan 2',
            'price_per_night' => 10000,
            'status'          => 'active',
            'commission_rate' => 15.0, // 15% Platform Commission
        ]);

        $room = Room::create([
            'property_id'     => $property->id,
            'name'            => 'Presidential Suite',
            'price_per_night' => 10000,
            'total_rooms'     => 5,
            'status'          => 'active',
        ]);

        // 3. Create 3 Bookings:
        // Booking 1: Confirmed with bKash (1.5% gateway fee) -> ৳20,000
        Booking::create([
            'booking_reference' => 'ACC-BK-1',
            'property_id'       => $property->id,
            'room_id'           => $room->id,
            'guest_name'        => 'Guest One',
            'guest_email'       => 'guest1@test.com',
            'guest_phone'       => '01711111111',
            'check_in'          => now()->addDays(2)->toDateString(),
            'check_out'         => now()->addDays(4)->toDateString(),
            'guests'            => 2,
            'nights'            => 2,
            'price_per_night'   => 10000,
            'subtotal'          => 20000,
            'tax_amount'        => 1500,
            'total_price'       => 21500,
            'total_amount'      => 21500,
            'currency'          => 'BDT',
            'payment_method'    => 'bkash',
            'payment_status'    => 'paid',
            'status'            => 'confirmed',
        ]);

        // Booking 2: Confirmed with Card (2.0% gateway fee) -> ৳10,000
        Booking::create([
            'booking_reference' => 'ACC-BK-2',
            'property_id'       => $property->id,
            'room_id'           => $room->id,
            'guest_name'        => 'Guest Two',
            'guest_email'       => 'guest2@test.com',
            'guest_phone'       => '01722222222',
            'check_in'          => now()->addDays(5)->toDateString(),
            'check_out'         => now()->addDays(6)->toDateString(),
            'guests'            => 1,
            'nights'            => 1,
            'price_per_night'   => 10000,
            'subtotal'          => 10000,
            'tax_amount'        => 750,
            'total_price'       => 10750,
            'total_amount'      => 10750,
            'currency'          => 'BDT',
            'payment_method'    => 'card',
            'payment_status'    => 'paid',
            'status'            => 'confirmed',
        ]);

        // Booking 3: Cancelled -> ৳5,000 (Should be excluded from Gross Booking Value)
        Booking::create([
            'booking_reference' => 'ACC-BK-3',
            'property_id'       => $property->id,
            'room_id'           => $room->id,
            'guest_name'        => 'Guest Three (Cancelled)',
            'guest_email'       => 'guest3@test.com',
            'guest_phone'       => '01733333333',
            'check_in'          => now()->addDays(10)->toDateString(),
            'check_out'         => now()->addDays(11)->toDateString(),
            'guests'            => 1,
            'nights'            => 1,
            'price_per_night'   => 5000,
            'subtotal'          => 5000,
            'tax_amount'        => 375,
            'total_price'       => 5375,
            'total_amount'      => 5375,
            'currency'          => 'BDT',
            'payment_method'    => 'pay_at_hotel',
            'status'            => 'cancelled',
        ]);

        $service = app(AccountingService::class);
        $kpis = $service->getOverviewKPIs();

        // 1. Order counts
        $this->assertEquals(3, $kpis['total_orders']);
        $this->assertEquals(2, $kpis['confirmed_orders']);
        $this->assertEquals(1, $kpis['cancelled_orders']);

        // 2. Gross Booking Value = 21,500 + 10,750 = 32,250
        $this->assertEquals(32250.0, $kpis['gross_booking_value']);

        // 3. Platform Commission (15%) = 32,250 * 0.15 = 4,837.50
        $this->assertEquals(4837.50, $kpis['platform_commission']);

        // 4. Vendor Payable (85%) = 32,250 * 0.85 = 27,412.50
        $this->assertEquals(27412.50, $kpis['vendor_payable']);

        // 5. Mathematical balance match: Commission + Vendor Payable === Gross Booking Value
        $this->assertEquals($kpis['gross_booking_value'], round($kpis['platform_commission'] + $kpis['vendor_payable'], 2));

        // 6. Net Profit > 0
        $this->assertGreaterThan(0, $kpis['net_profit']);
    }

    public function test_monthly_pnl_chart_data_generation(): void
    {
        $service = app(AccountingService::class);
        $year = (int) date('Y');
        $chartData = $service->getMonthlyPnLChartData($year);

        $this->assertIsArray($chartData);
        $this->assertCount(12, $chartData['months']);
        $this->assertCount(12, $chartData['revenue']);
        $this->assertCount(12, $chartData['commission']);
        $this->assertCount(12, $chartData['payouts']);
    }

    public function test_vendor_statements_and_available_balance(): void
    {
        $vendor = User::create([
            'name'     => 'Ocean Paradise Vendor',
            'email'    => 'vendor.ocean@test.com',
            'password' => bcrypt('password'),
            'role'     => 'vendor',
        ]);

        $property = Property::create([
            'vendor_id'       => $vendor->id,
            'name'            => 'Ocean Paradise Resort',
            'slug'            => 'ocean-paradise-' . uniqid(),
            'type'            => 'Resort',
            'city'            => "Cox's Bazar",
            'address'         => 'Kolatoli Road',
            'price_per_night' => 8000,
            'status'          => 'active',
            'commission_rate' => 10.0, // 10% commission
        ]);

        // Create a 10,000 BDT booking (Net payable = 9,000 BDT)
        Booking::create([
            'booking_reference' => 'ACC-OCEAN-1',
            'property_id'       => $property->id,
            'guest_name'        => 'Guest Ocean',
            'guest_email'       => 'guest.ocean@test.com',
            'guest_phone'       => '01700000000',
            'check_in'          => now()->addDays(1)->toDateString(),
            'check_out'         => now()->addDays(2)->toDateString(),
            'guests'            => 1,
            'nights'            => 1,
            'price_per_night'   => 10000,
            'subtotal'          => 10000,
            'tax_amount'        => 0,
            'total_price'       => 10000,
            'total_amount'      => 10000,
            'currency'          => 'BDT',
            'status'            => 'confirmed',
        ]);

        // Record a 3,000 BDT Payout to vendor
        Payout::create([
            'vendor_id'      => $vendor->id,
            'amount'         => 3000.0,
            'status'         => 'paid',
            'payment_method' => 'bank_transfer',
            'processed_at'   => now(),
        ]);

        $service = app(AccountingService::class);
        $statements = $service->getVendorStatements($vendor->id);

        $this->assertNotEmpty($statements);
        $vData = $statements->first();
        $this->assertNotNull($vData->finance_stats);

        // Gross sales = 10,000
        $this->assertEquals(10000.0, $vData->finance_stats->gross_sales);
        // Commission (10%) = 1,000
        $this->assertEquals(1000.0, $vData->finance_stats->commission_deducted);
        // Net Payable (90%) = 9,000
        $this->assertEquals(9000.0, $vData->finance_stats->net_payable);
        // Payouts Paid = 3,000
        $this->assertEquals(3000.0, $vData->finance_stats->payouts_paid);
        // Available Balance = 9,000 - 3,000 = 6,000
        $this->assertEquals(6000.0, $vData->finance_stats->available_balance);
    }

    /**
     * 4. Test Zero-Leakage Trial Balance Mathematical Invariant.
     */
    public function test_trial_balance_mathematical_invariant_reconciliation(): void
    {
        $service = app(AccountingService::class);
        $audit = $service->auditTrialBalanceIntegrity();

        $this->assertIsArray($audit);
        $this->assertTrue($audit['is_balanced']);
        $this->assertEquals(0.00, $audit['discrepancy_amount']);
        $this->assertEquals('PERFECT_MATCH', $audit['status']);
    }

    /**
     * 5. Test Ledger Immutability & Anti-Tampering Scanner.
     */
    public function test_ledger_immutability_and_tamper_proofing(): void
    {
        $service = app(AccountingService::class);
        $res = $service->verifyLedgerImmutability();

        $this->assertIsArray($res);
        $this->assertTrue($res['is_tamper_free']);
        $this->assertEquals('SECURE_IMMUTABLE', $res['status']);
        $this->assertEquals(0, $res['corrupt_records']);
    }

    /**
     * 6. Test Atomic Vendor Payout with Zero Double-Spend Guarantee.
     */
    public function test_atomic_payout_with_double_spend_prevention(): void
    {
        $vendor = User::create([
            'name'     => 'Direct Payout Vendor',
            'email'    => 'vendor.direct@test.com',
            'password' => bcrypt('password'),
            'role'     => 'vendor',
        ]);

        $property = Property::create([
            'vendor_id'       => $vendor->id,
            'name'            => 'Direct Payout Villa',
            'slug'            => 'direct-payout-villa-' . uniqid(),
            'type'            => 'Villa',
            'city'            => 'Sylhet',
            'address'         => 'Airport Road',
            'price_per_night' => 10000,
            'status'          => 'active',
            'commission_rate' => 10.0,
        ]);

        // Create booking: 20,000 total (Net payable = 18,000)
        Booking::create([
            'booking_reference' => 'ACC-DIRECT-1',
            'property_id'       => $property->id,
            'guest_name'        => 'Direct Guest',
            'guest_email'       => 'direct@test.com',
            'guest_phone'       => '01711111111',
            'check_in'          => now()->subDays(5)->toDateString(),
            'check_out'         => now()->subDays(3)->toDateString(),
            'guests'            => 2,
            'nights'            => 2,
            'price_per_night'   => 10000,
            'subtotal'          => 20000,
            'tax_amount'        => 0,
            'total_price'       => 20000,
            'total_amount'      => 20000,
            'currency'          => 'BDT',
            'status'            => 'confirmed',
        ]);

        $service = app(AccountingService::class);

        // Attempt 1: Over-withdrawal attempt (Try to withdraw 25,000 when only 18,000 available) -> MUST FAIL
        $fraudAttempt = $service->processSecureVendorPayout(
            $vendor->id,
            25000.0,
            'bkash',
            ['phone' => '01711111111']
        );
        $this->assertFalse($fraudAttempt['success']);
        $this->assertStringContainsString('Insufficient withdrawable balance', $fraudAttempt['message']);

        // Attempt 2: Valid withdrawal (Withdraw 10,000 out of 18,000) -> MUST SUCCEED
        $validPayout = $service->processSecureVendorPayout(
            $vendor->id,
            10000.0,
            'bkash',
            ['phone' => '01711111111']
        );
        $this->assertTrue($validPayout['success']);
        $this->assertEquals(8000.0, $validPayout['remaining_balance']);

        // Assert Debit record exists in accounting_ledgers
        $this->assertDatabaseHas('accounting_ledgers', [
            'vendor_id'    => $vendor->id,
            'type'         => 'payout',
            'gross_amount' => 10000.0,
        ]);
    }

    /**
     * 7. Test Daily Financial Reconciliation Artisan Command (finance:reconcile-daily).
     */
    public function test_daily_financial_reconciliation_artisan_command(): void
    {
        $this->artisan('finance:reconcile-daily')
            ->expectsOutputToContain('Starting automated daily financial reconciliation audit')
            ->assertExitCode(0);
    }

    /**
     * 8. Test Member Secret Deals Engine.
     */
    public function test_member_secret_deals_calculation(): void
    {
        $dealService = app(\App\Services\MemberDealsService::class);

        // Guest user (Unauthenticated) -> No active secret deal, shows CTA
        $guestDeal = $dealService->getDealDetails(10000.0, null);
        $this->assertFalse($guestDeal['is_member']);
        $this->assertEquals(10000.0, $guestDeal['final_price']);
        $this->assertNotNull($guestDeal['unlock_cta']);

        // Logged-in Member -> Gets Base Member Secret Discount (8%)
        $member = User::create([
            'name'     => 'Deal Member',
            'email'    => 'member.deal@test.com',
            'password' => bcrypt('password'),
            'role'     => 'user',
        ]);
        $memberDeal = $dealService->getDealDetails(10000.0, $member);
        $this->assertTrue($memberDeal['is_member']);
        $this->assertTrue($memberDeal['has_active_deal']);
        $this->assertEquals(9200.0, $memberDeal['final_price']); // 8% off 10,000 = 800 savings
        $this->assertEquals(800.0, $memberDeal['savings_amount']);
    }

    /**
     * 9. Test NBR Monthly Tax Deduction (TDS/VAT) Certificate Generation.
     */
    public function test_monthly_tax_certificate_generation(): void
    {
        $vendor = User::create([
            'name'     => 'Tax Certified Hotelier',
            'email'    => 'tax.hotelier@test.com',
            'password' => bcrypt('password'),
            'role'     => 'vendor',
        ]);

        $service = app(AccountingService::class);
        $cert = $service->generateMonthlyTaxCertificate($vendor->id, (int)date('Y'), (int)date('m'));

        $this->assertIsArray($cert);
        $this->assertEquals($vendor->id, $cert['vendor_id']);
        $this->assertStringStartsWith('TDS-PB-', $cert['certificate_number']);
        $this->assertEquals('NBR_INCOME_TAX_ACT_2023_SECTION_52', $cert['tax_compliance']);
    }

    /**
     * 10. Test FinTech Watchdog Command & HMAC Cryptographic Audit (finance:watchdog).
     */
    public function test_fintech_watchdog_command_and_hmac_cryptographic_audit(): void
    {
        $this->artisan('finance:watchdog')
            ->expectsOutputToContain('PRIME BOOKING — Enterprise FinTech Security Watchdog')
            ->expectsOutputToContain('ZERO LEAKAGE (MATCHED)')
            ->assertExitCode(0);

        // JSON format test for external cloud telemetry (Datadog/Prometheus)
        $this->artisan('finance:watchdog --json')
            ->assertExitCode(0);
    }

    /**
     * 11. Test Stripe/FinTech Standard Idempotency Middleware.
     */
    public function test_idempotency_middleware_prevents_duplicate_mutations(): void
    {
        $middleware = new \App\Http\Middleware\IdempotencyMiddleware();
        $key = 'test-idemp-' . uniqid();

        $request = \Illuminate\Http\Request::create('/api/v1/accounts/expense', 'POST', [
            'category'       => 'test_fee',
            'amount'         => 100,
            'payment_method' => 'bkash',
            'description'    => 'Idempotent test',
        ]);
        $request->headers->set('Idempotency-Key', $key);

        $response1 = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true, 'execution_time' => microtime(true)], 200);
        });

        // Exact second request with the same Idempotency-Key MUST return the cached replay
        $response2 = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true, 'execution_time' => 'new_execution'], 200);
        });

        $this->assertEquals(200, $response1->getStatusCode());
        $this->assertEquals(200, $response2->getStatusCode());
        $this->assertEquals('true', $response2->headers->get('X-Idempotency-Replay'));
    }

    /**
     * 12. Test Agoda-Style Room Upsell Upgrade Matrix.
     */
    public function test_room_upsell_service_upgrade_matrix(): void
    {
        $vendor = User::create([
            'name'     => 'Upsell Vendor',
            'email'    => 'upsell.vendor@test.com',
            'password' => bcrypt('password'),
            'role'     => 'vendor',
        ]);

        $property = Property::create([
            'vendor_id'       => $vendor->id,
            'name'            => 'Upsell Resort',
            'slug'            => 'upsell-resort-' . uniqid(),
            'type'            => 'Resort',
            'city'            => 'Coxsbazar',
            'price_per_night' => 5000,
            'status'          => 'active',
        ]);

        $stdRoom = Room::create([
            'property_id'     => $property->id,
            'name'            => 'Standard Room',
            'price_per_night' => 5000,
            'capacity'        => 2,
            'status'          => 'active',
        ]);

        $dlxRoom = Room::create([
            'property_id'     => $property->id,
            'name'            => 'Deluxe Sea View',
            'price_per_night' => 5800,
            'capacity'        => 2,
            'status'          => 'active',
        ]);

        $upsellService = app(\App\Services\RoomUpsellService::class);
        $upgrades = $upsellService->getUpgradeRecommendations($property, $stdRoom, 2);

        $this->assertNotEmpty($upgrades);
        $this->assertEquals($dlxRoom->id, $upgrades[0]['room_id']);
        $this->assertEquals(800.0, $upgrades[0]['upgrade_diff_night']);
        $this->assertEquals(1600.0, $upgrades[0]['total_upgrade_cost']);
    }

    /**
     * 13. Test Universal Payment Gateway Webhook (bKash / SSLCommerz / Bank API).
     * Automatically settles booking, ledger, loyalty points, and inventory.
     */
    public function test_payment_gateway_webhook_auto_settles_ledger_points_and_inventory(): void
    {
        $user = User::create([
            'name'     => 'Webhook Payer',
            'email'    => 'payer.webhook@test.com',
            'password' => bcrypt('password'),
            'role'     => 'user',
        ]);

        $vendor = User::create([
            'name'     => 'Webhook Vendor',
            'email'    => 'vendor.webhook@test.com',
            'password' => bcrypt('password'),
            'role'     => 'vendor',
        ]);

        $property = Property::create([
            'vendor_id'       => $vendor->id,
            'name'            => 'Webhook Hotel',
            'slug'            => 'webhook-hotel-' . uniqid(),
            'type'            => 'Hotel',
            'city'            => 'Dhaka',
            'price_per_night' => 10000,
            'status'          => 'active',
            'commission_rate' => 10.0,
        ]);

        $booking = Booking::create([
            'booking_reference' => 'WH-TEST-' . strtoupper(uniqid()),
            'user_id'           => $user->id,
            'property_id'       => $property->id,
            'guest_name'        => 'Webhook Payer',
            'guest_email'       => 'payer.webhook@test.com',
            'guest_phone'       => '01711111111',
            'check_in'          => now()->addDays(2)->toDateString(),
            'check_out'         => now()->addDays(4)->toDateString(),
            'guests'            => 2,
            'nights'            => 2,
            'price_per_night'   => 10000,
            'subtotal'          => 20000,
            'tax_amount'        => 0,
            'total_price'       => 20000,
            'total_amount'      => 20000,
            'currency'          => 'BDT',
            'payment_status'    => 'pending',
            'status'            => 'pending',
        ]);

        // Post simulated bKash/SSLCommerz Payment Callback
        $response = $this->postJson('/api/v1/payment/callback/bkash', [
            'tran_id'           => $booking->booking_reference,
            'status'            => 'success',
            'trxID'             => 'TRX-BKASH-998877',
            'transactionStatus' => 'Completed',
            'amount'            => 20000,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // 1. Assert Booking is marked PAID & CONFIRMED
        $booking->refresh();
        $this->assertEquals('paid', $booking->payment_status);
        $this->assertEquals('confirmed', $booking->status);

        // 2. Assert Double-Entry General Ledger was auto-written with HMAC seal
        $this->assertDatabaseHas('accounting_ledgers', [
            'booking_id'   => $booking->id,
            'vendor_id'    => $vendor->id,
            'type'         => 'credit',
            'gross_amount' => 20000.0,
            'status'       => 'completed',
        ]);

        $ledger = AccountingLedger::where('booking_id', $booking->id)->first();
        $this->assertNotNull($ledger->metadata['cryptographic_seal']);

        // 3. Assert User Loyalty Points were auto-credited (20,000 / 1000 = 20 points)
        $this->assertDatabaseHas('user_rewards', [
            'user_id'        => $user->id,
            'points_balance' => 20,
        ]);
    }
}
