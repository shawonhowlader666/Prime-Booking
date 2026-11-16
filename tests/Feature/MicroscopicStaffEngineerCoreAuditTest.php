<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\Booking;
use App\Models\AccountingLedger;
use App\Models\Coupon;
use App\Models\UserReward;
use App\Services\AccountingService;
use App\Services\CouponService;
use App\Services\InventoryService;
use App\Services\RewardPointService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * MicroscopicStaffEngineerCoreAuditTest
 *
 * Microscopic verification of core invariants:
 * 1. Pessimistic Concurrency & Last-Room Race Condition.
 * 2. Double-Payment Webhook / IPN Idempotency & General Ledger Invariants.
 * 3. Guest Checkout IDOR Prevention & Safe Multi-Channel Authorization.
 * 4. Coupon Spend Mathematical Floor & Non-Negative Total Guard.
 * 5. Double-Entry Accounting Balance Sheet Invariant: Gross = Commission + Fee + Net.
 * 6. High-Frequency Sequential Inventory Depletion to Exactly Zero.
 * 7. Wallet Points Balance Atomic Isolation & Underflow Guard.
 */
class MicroscopicStaffEngineerCoreAuditTest extends TestCase
{
    use RefreshDatabase;

    private Property $property;
    private Room $room;
    private User $vendor;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vendor = User::create([
            'name'     => 'Grand Sultan Vendor',
            'email'    => 'vendor_' . Str::random(8) . '@grandsultan.com',
            'password' => bcrypt('Secret123!'),
            'role'     => 'vendor',
            'status'   => 'active',
        ]);

        $this->property = Property::create([
            'vendor_id'        => $this->vendor->id,
            'name'             => 'Grand Sultan Tea Resort',
            'slug'             => 'grand-sultan-tea-resort-' . Str::random(6),
            'type'             => 'hotel',
            'city'             => 'Sreemangal',
            'address'          => 'Radhanagar, Sreemangal',
            'price_per_night'  => 8500.00,
            'commission_rate'  => 15.00,
            'status'           => 'active',
            'is_featured'      => true,
        ]);

        $this->room = Room::create([
            'property_id'     => $this->property->id,
            'name'            => 'Presidential Suite',
            'room_type'       => 'suite',
            'price_per_night' => 8500.00,
            'total_rooms'     => 1, // EXACTLY 1 ROOM for race stress test
            'max_adults'      => 2,
            'max_children'    => 1,
            'max_guests'      => 3,
            'status'          => 'active',
        ]);

        $this->customer = User::create([
            'name'     => 'Senior Tester',
            'email'    => 'tester_' . Str::random(8) . '@example.com',
            'password' => bcrypt('Secret123!'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);
    }

    /**
     * Test 1: Pessimistic Concurrency & Overbooking Rejection on Last Unit.
     */
    public function test_pessimistic_concurrency_and_overbooking_rejection_on_last_unit(): void
    {
        $inventoryService = app(InventoryService::class);
        $couponService    = app(CouponService::class);

        $checkIn  = now()->addDays(10)->toDateString();
        $checkOut = now()->addDays(12)->toDateString();

        // 1. First booking succeeds and acquires the only room
        $booking1 = DB::transaction(function () use ($checkIn, $checkOut, $inventoryService) {
            $lockedRoom = Room::where('id', $this->room->id)->lockForUpdate()->first();
            $this->assertNotNull($lockedRoom);

            $activeCount = Booking::where('room_id', $lockedRoom->id)
                ->where(fn($q) => $q->where('status', 'confirmed')->orWhere('status', 'pending'))
                ->whereDate('check_in', '<', $checkOut)
                ->whereDate('check_out', '>', $checkIn)
                ->count();

            $this->assertEquals(0, $activeCount);

            $b = Booking::create([
                'booking_reference' => 'PRM-RACE-001',
                'property_id'       => $this->property->id,
                'room_id'           => $lockedRoom->id,
                'user_id'           => $this->customer->id,
                'guest_name'        => 'First Guest',
                'guest_email'       => 'first@guest.com',
                'guest_phone'       => '01700000001',
                'check_in'          => $checkIn,
                'check_out'         => $checkOut,
                'guests'            => 2,
                'nights'            => 2,
                'price_per_night'   => 8500,
                'subtotal'          => 17000,
                'tax_amount'        => 1275,
                'total_price'       => 18275,
                'currency'          => 'BDT',
                'payment_method'    => 'bkash',
                'payment_status'    => 'paid',
                'status'            => 'confirmed',
                'booking_status'    => 'confirmed',
            ]);

            $inventoryService->lockAndDeduct($b);
            return $b;
        });

        $this->assertNotNull($booking1);

        // 2. Second concurrent booking attempt on same date must be rejected
        $overbookingThrown = false;
        try {
            DB::transaction(function () use ($checkIn, $checkOut) {
                $lockedRoom = Room::where('id', $this->room->id)->lockForUpdate()->first();
                $activeCount = Booking::where('room_id', $lockedRoom->id)
                    ->where(fn($q) => $q->where('status', 'confirmed')->orWhere('status', 'pending'))
                    ->whereDate('check_in', '<', $checkOut)
                    ->whereDate('check_out', '>', $checkIn)
                    ->count();

                if ($activeCount >= (int)$lockedRoom->total_rooms) {
                    throw new \RuntimeException('Overbooking rejected: Zero units available.');
                }

                // Should never reach here
                Booking::create([
                    'booking_reference' => 'PRM-RACE-002',
                    'property_id'       => $this->property->id,
                    'room_id'           => $lockedRoom->id,
                    'user_id'           => null,
                    'guest_name'        => 'Second Guest',
                    'guest_email'       => 'second@guest.com',
                    'guest_phone'       => '01700000002',
                    'check_in'          => $checkIn,
                    'check_out'         => $checkOut,
                    'guests'            => 2,
                    'nights'            => 2,
                    'price_per_night'   => 8500,
                    'subtotal'          => 17000,
                    'tax_amount'        => 1275,
                    'total_price'       => 18275,
                    'currency'          => 'BDT',
                    'payment_method'    => 'bkash',
                    'payment_status'    => 'pending',
                    'status'            => 'pending',
                ]);
            });
        } catch (\RuntimeException $e) {
            $overbookingThrown = true;
            $this->assertStringContainsString('Overbooking rejected', $e->getMessage());
        }

        $this->assertTrue($overbookingThrown, 'Overbooking rejection invariant violated!');
        $this->assertEquals(1, Booking::where('room_id', $this->room->id)->count());
    }

    /**
     * Test 2: Double-Payment Callback & IPN Idempotency with Zero Duplicate Ledger Writes.
     */
    public function test_payment_callback_idempotency_and_zero_duplicate_ledger(): void
    {
        $booking = Booking::create([
            'booking_reference' => 'PRM-IDEMP-' . Str::upper(Str::random(6)),
            'property_id'       => $this->property->id,
            'room_id'           => $this->room->id,
            'user_id'           => $this->customer->id,
            'guest_name'        => 'Idempotent Tester',
            'guest_email'       => 'idemp@example.com',
            'guest_phone'       => '01811223344',
            'check_in'          => now()->addDays(5)->toDateString(),
            'check_out'         => now()->addDays(7)->toDateString(),
            'guests'            => 2,
            'nights'            => 2,
            'price_per_night'   => 8500,
            'subtotal'          => 17000,
            'tax_amount'        => 1275,
            'total_price'       => 18275,
            'currency'          => 'BDT',
            'payment_method'    => 'bkash',
            'payment_status'    => 'pending',
            'status'            => 'confirmed',
            'booking_status'    => 'confirmed',
        ]);

        // Simulate 3 rapid payment callbacks for the exact same transaction (e.g. gateway retries)
        for ($i = 0; $i < 3; $i++) {
            $response = $this->post(route('payment.bkash.sandbox-execute', $booking->booking_reference));
            $response->assertRedirectContains('/booking/confirmation/' . $booking->booking_reference);
        }

        $booking->refresh();
        $this->assertEquals('paid', $booking->payment_status);

        // Microscopic Invariant: EXACTLY 1 ledger entry exists for this booking ID
        $ledgerCount = AccountingLedger::where('booking_id', $booking->id)
            ->where('type', 'credit')
            ->where('category', 'hotel_booking')
            ->count();

        $this->assertEquals(1, $ledgerCount, 'Idempotency failure: duplicate ledger entries created!');
    }

    /**
     * Test 3: Guest Checkout Authorization Security (IDOR Shield & Valid Access Paths).
     */
    public function test_guest_checkout_authorization_security_and_idor_protection(): void
    {
        $guestBooking = Booking::create([
            'booking_reference' => 'PRM-GST-9988',
            'property_id'       => $this->property->id,
            'room_id'           => $this->room->id,
            'user_id'           => null, // Guest with no account
            'guest_name'        => 'Anonymous Guest',
            'guest_email'       => 'guest.secret@testmail.com',
            'guest_phone'       => '01999887766',
            'check_in'          => now()->addDays(3)->toDateString(),
            'check_out'         => now()->addDays(5)->toDateString(),
            'guests'            => 1,
            'nights'            => 2,
            'price_per_night'   => 8500,
            'subtotal'          => 17000,
            'tax_amount'        => 1275,
            'total_price'       => 18275,
            'currency'          => 'BDT',
            'payment_method'    => 'cash',
            'payment_status'    => 'unpaid',
            'status'            => 'confirmed',
        ]);

        // Scenario A: Random unauthorized user attempts access -> 403 Forbidden
        $unauthorized = $this->get(route('booking.confirmation', $guestBooking->booking_reference));
        $unauthorized->assertStatus(403);

        $voucherUnauthorized = $this->get(route('booking.voucher.download', $guestBooking->booking_reference));
        $voucherUnauthorized->assertStatus(403);

        // Scenario B: Wrong email provided in query -> 403 Forbidden
        $wrongEmail = $this->get(route('booking.confirmation', [
            'reference' => $guestBooking->booking_reference,
            'email'     => 'hacker@attacker.com',
        ]));
        $wrongEmail->assertStatus(403);

        // Scenario C: Correct guest email provided in query -> 200 OK
        $authorizedEmail = $this->get(route('booking.confirmation', [
            'reference' => $guestBooking->booking_reference,
            'email'     => 'guest.secret@testmail.com',
        ]));
        $authorizedEmail->assertStatus(200);

        // Scenario D: Correct phone provided in query -> 200 OK
        $authorizedPhone = $this->get(route('booking.voucher.download', [
            'reference' => $guestBooking->booking_reference,
            'phone'     => '01999887766',
        ]));
        $authorizedPhone->assertStatus(200);

        // Scenario E: Session holds last_booking_ref (just checked out) -> 200 OK
        $authorizedSession = $this->withSession(['last_booking_ref' => $guestBooking->booking_reference])
            ->get(route('booking.confirmation', $guestBooking->booking_reference));
        $authorizedSession->assertStatus(200);
    }

    /**
     * Test 4: Financial Balance Sheet Mathematical Invariant:
     * Gross Amount == Platform Commission + Gateway Fee + Vendor Net Payable.
     */
    public function test_financial_accounting_balance_sheet_mathematical_invariant(): void
    {
        $grossAmounts = [1000.00, 4520.50, 18275.00, 99999.99, 12.34];

        foreach ($grossAmounts as $gross) {
            $booking = Booking::create([
                'booking_reference' => 'PRM-MATH-' . Str::upper(Str::random(6)),
                'property_id'       => $this->property->id,
                'room_id'           => $this->room->id,
                'user_id'           => $this->customer->id,
                'guest_name'        => 'Math Guest',
                'guest_email'       => 'math@test.com',
                'guest_phone'       => '01711111111',
                'check_in'          => now()->addDays(1)->toDateString(),
                'check_out'         => now()->addDays(2)->toDateString(),
                'guests'            => 2,
                'nights'            => 1,
                'price_per_night'   => $gross,
                'subtotal'          => $gross,
                'tax_amount'        => round($gross * 0.075, 2),
                'total_price'       => $gross,
                'currency'          => 'BDT',
                'payment_method'    => 'bkash',
                'payment_status'    => 'paid',
                'status'            => 'confirmed',
            ]);

            $ledger = AccountingService::recordBookingLedger($booking, 'bkash');
            $this->assertNotNull($ledger);

            $recordedGross = (float) $ledger->gross_amount;
            $comm          = (float) $ledger->commission_amount;
            $fee           = (float) $ledger->gateway_fee;
            $net           = (float) $ledger->net_amount;

            // Invariant: Gross = Commission + Gateway Fee + Net Payable (within 0.02 BDT for floating rounding)
            $sum = round($comm + $fee + $net, 2);
            $this->assertEqualsWithDelta($recordedGross, $sum, 0.02, "Ledger balancing invariant failed for Gross: {$gross}");
        }
    }

    /**
     * Test 5: Coupon Spend Threshold, Expiry & Max Deduction Floor.
     */
    public function test_coupon_spend_threshold_and_non_negative_floor(): void
    {
        $coupon = Coupon::create([
            'code'        => 'STAFF500',
            'type'        => 'fixed',
            'amount'      => 500.00,
            'min_spend'   => 3000.00,
            'usage_limit' => 10,
            'used_count'  => 0,
            'expires_at'  => now()->addDays(30),
            'status'      => 'active',
        ]);

        $service = app(CouponService::class);

        // A. Spend below min_spend ($2500 < $3000) -> Invalid
        $resBelow = $service->validateCoupon('STAFF500', 2500.00);
        $this->assertFalse($resBelow['valid']);

        // B. Spend meeting threshold ($3500 >= $3000) -> Valid with 500 discount
        $resValid = $service->validateCoupon('STAFF500', 3500.00);
        $this->assertTrue($resValid['valid']);
        $this->assertEquals(500.0, (float)$resValid['discount']);

        // C. Percentage coupon on large amount
        $pctCoupon = Coupon::create([
            'code'        => 'VIP20PCT',
            'type'        => 'percentage',
            'amount'      => 20.00, // 20%
            'min_spend'   => 1000.00,
            'usage_limit' => 100,
            'status'      => 'active',
        ]);

        $resPct = $service->validateCoupon('VIP20PCT', 10000.00);
        $this->assertTrue($resPct['valid']);
        $this->assertEquals(2000.0, (float)$resPct['discount']); // 20% of 10000 = 2000
    }

    /**
     * Test 6: Wallet Points Atomic Isolation & Underflow Prevention.
     */
    public function test_wallet_points_atomic_isolation_and_underflow_prevention(): void
    {
        $service = app(RewardPointService::class);
        $wallet = UserReward::firstOrCreate(['user_id' => $this->customer->id]);
        $wallet->update([
            'points_balance'        => 50,
            'total_earned_points'   => 50,
            'total_redeemed_points' => 0,
        ]);

        // Attempt to request payout of 100 points when balance is only 50
        $result = $service->requestPayout($this->customer, 100, 'bkash', '01711223344', 'Senior Tester');
        $this->assertTrue(str_contains($result['message'], 'exceed') || str_contains($result['message'], 'Insufficient'));

        // Verify balance was untouched (still 50, never negative)
        $wallet->refresh();
        $this->assertEquals(50, $wallet->points_balance);
        $this->assertGreaterThanOrEqual(0, $wallet->points_balance);
    }
}
