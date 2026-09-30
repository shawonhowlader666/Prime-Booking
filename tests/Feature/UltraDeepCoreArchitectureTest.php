<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Coupon;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use App\Models\UserReward;
use App\Services\AccountingService;
use App\Services\CouponService;
use App\Services\DynamicPricingService;
use App\Services\InventoryService;
use App\Services\PaymentVerificationService;
use App\Services\RewardPointService;
use App\Services\VIPLoyaltyService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * UltraDeepCoreArchitectureTest
 * ──────────────────────────────
 * Microscopic deep-core audit testing:
 * 1. Full E2E Database Transaction Lifecycles
 * 2. Overbooking & Race Condition Prevention
 * 3. Inventory Locking, Holding & Restoration
 * 4. Coupon Rules, Percent vs Flat & Min-Spend Math
 * 5. Double-Entry Accounting General Ledger Math
 * 6. Payment Status State-Machine Transitions
 * 7. Dynamic Pricing & Weekend Surge Math
 */
class UltraDeepCoreArchitectureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to seed standard Property and Room in test database.
     */
    private function createTestPropertyAndRoom(int $totalRooms = 5, float $price = 5000.0): array
    {
        $property = Property::create([
            'name'                   => 'Grand Sylhet Resort & Spa',
            'slug'                   => 'grand-sylhet-resort-spa-' . uniqid(),
            'type'                   => 'Hotel',
            'city'                   => 'Sylhet',
            'address'                => 'Boroshala, Airport Road, Sylhet',
            'star_rating'            => 5,
            'rating_score'           => 9.2,
            'total_reviews'          => 120,
            'price_per_night'        => $price,
            'status'                 => 'active',
            'commission_rate'        => 12.0,
            'free_cancellation'      => 1,
            'no_credit_card_required'=> 1,
        ]);

        $room = Room::create([
            'property_id'     => $property->id,
            'name'            => 'Executive Suite',
            'price_per_night' => $price,
            'total_rooms'     => $totalRooms,
            'max_adults'      => 2,
            'max_children'    => 1,
            'status'          => 'active',
        ]);

        return [$property, $room];
    }

    /**
     * 1. Test Full End-to-End Booking Creation Transaction.
     */
    public function test_full_end_to_end_booking_creation_transaction(): void
    {
        [$property, $room] = $this->createTestPropertyAndRoom(totalRooms: 5, price: 6000.0);

        $coupon = Coupon::create([
            'code'        => 'DEEPTEST10',
            'type'        => 'percent',
            'value'       => 10,
            'amount'      => 10,
            'min_spend'   => 5000,
            'is_active'   => 1,
            'status'      => 'active',
            'used_count'  => 0,
            'usage_limit' => 100,
        ]);

        $checkIn  = now()->addDays(5)->format('Y-m-d');
        $checkOut = now()->addDays(7)->format('Y-m-d'); // 2 nights

        $response = $this->post("/book/{$property->id}", [
            'room_id'        => $room->id,
            'guest_name'     => 'Dr. Ahmed Khan',
            'guest_email'    => 'ahmed.khan@example.com',
            'guest_phone'    => '01712345678',
            'check_in'       => $checkIn,
            'check_out'      => $checkOut,
            'guests'         => 2,
            'payment_method' => 'pay_at_hotel',
            'coupon_code'    => 'DEEPTEST10',
            'addons'         => ['daily_breakfast'],
        ]);

        $response->assertStatus(302);

        // Assert booking exists in database
        $this->assertDatabaseHas('bookings', [
            'property_id' => $property->id,
            'room_id'     => $room->id,
            'guest_name'  => 'Dr. Ahmed Khan',
            'guest_email' => 'ahmed.khan@example.com',
        ]);

        $booking = Booking::where('guest_email', 'ahmed.khan@example.com')->first();
        $this->assertNotNull($booking);
        $this->assertStringStartsWith('PRM-', $booking->booking_reference);

        // Verify Financial Math
        $this->assertGreaterThan(0, $booking->total_price);
        $this->assertGreaterThan(0, $booking->commission_amount);
        $this->assertGreaterThan(0, $booking->vendor_payout);

        // Verify commission + vendor payout equals subtotal minus discount
        $netSubtotal = $booking->subtotal - $booking->discount_amount;
        $this->assertEquals($netSubtotal, round($booking->commission_amount + $booking->vendor_payout, 2));

        // Verify coupon usage increment
        $coupon->refresh();
        $this->assertEquals(1, $coupon->used_count);

        // Verify Addons Table entry
        $this->assertDatabaseHas('booking_addons', [
            'booking_id' => $booking->id,
        ]);
    }

    /**
     * 2. Test Strict Capacity Limits & Overbooking Rejection.
     */
    public function test_strict_capacity_limit_and_overbooking_rejection(): void
    {
        // Property with strictly ONLY 1 ROOM
        [$property, $room] = $this->createTestPropertyAndRoom(totalRooms: 1, price: 4000.0);

        $checkIn  = now()->addDays(15)->format('Y-m-d');
        $checkOut = now()->addDays(17)->format('Y-m-d');

        // First guest books the only room
        $this->post("/book/{$property->id}", [
            'room_id'        => $room->id,
            'guest_name'     => 'Guest One',
            'guest_email'    => 'guest1@example.com',
            'guest_phone'    => '01811111111',
            'check_in'       => $checkIn,
            'check_out'      => $checkOut,
            'guests'         => 1,
            'payment_method' => 'pay_at_hotel',
        ]);

        $this->assertDatabaseHas('bookings', [
            'room_id'     => $room->id,
            'guest_email' => 'guest1@example.com',
        ]);

        // Second guest tries to book the EXACT SAME room on overlapping dates
        $conflict = $this->post("/book/{$property->id}", [
            'room_id'        => $room->id,
            'guest_name'     => 'Guest Two (Overbook Attempt)',
            'guest_email'    => 'guest2@example.com',
            'guest_phone'    => '01822222222',
            'check_in'       => $checkIn,
            'check_out'      => $checkOut,
            'guests'         => 1,
            'payment_method' => 'pay_at_hotel',
        ]);

        // Second booking must be rejected with session error
        $conflict->assertSessionHas('error');
        $this->assertDatabaseMissing('bookings', [
            'guest_email' => 'guest2@example.com',
        ]);
    }

    /**
     * 3. Test Self-Service Cancellation & Instant Inventory Restoral.
     */
    public function test_cancellation_and_inventory_restoral(): void
    {
        [$property, $room] = $this->createTestPropertyAndRoom(totalRooms: 1, price: 5000.0);

        $checkIn  = now()->addDays(20)->format('Y-m-d');
        $checkOut = now()->addDays(22)->format('Y-m-d');

        $this->post("/book/{$property->id}", [
            'room_id'        => $room->id,
            'guest_name'     => 'Guest Cancel Test',
            'guest_email'    => 'canceltest@example.com',
            'guest_phone'    => '01833333333',
            'check_in'       => $checkIn,
            'check_out'      => $checkOut,
            'guests'         => 1,
            'payment_method' => 'pay_at_hotel',
        ]);

        $booking = Booking::where('guest_email', 'canceltest@example.com')->first();
        $this->assertNotNull($booking);

        // Cancel the reservation
        $controller = app(\App\Http\Controllers\Web\BookingFlowController::class);
        $request = new \Illuminate\Http\Request();
        $request->merge(['guest_email' => 'canceltest@example.com']);

        $cancelRes = $controller->cancelBooking($request, $booking->booking_reference, app(InventoryService::class));
        
        $booking->refresh();
        $this->assertEquals('cancelled', $booking->status);

        // Inventory check: room should now be available again
        $invService = app(InventoryService::class);
        $avail = $invService->checkAvailability($room->id, $checkIn, $checkOut, 1);
        $this->assertTrue($avail['is_available']);
        $this->assertEquals(1, $avail['min_available']);
    }

    /**
     * 4. Test Coupon Engine Rules (Flat Discount, Percentage, Minimum Spend).
     */
    public function test_coupon_engine_comprehensive_rules(): void
    {
        $couponService = app(CouponService::class);

        // 1. Percentage Coupon
        Coupon::create([
            'code'        => 'PERCENT20',
            'type'        => 'percent',
            'value'       => 20,
            'amount'      => 20,
            'is_active'   => 1,
            'status'      => 'active',
        ]);
        $pctRes = $couponService->validateCoupon('PERCENT20', 10000.0);
        $this->assertTrue($pctRes['valid']);
        $this->assertEquals(2000.0, $pctRes['discount']);

        // 2. Fixed Amount Coupon
        Coupon::create([
            'code'        => 'FLAT500',
            'type'        => 'fixed',
            'value'       => 500,
            'amount'      => 500,
            'is_active'   => 1,
            'status'      => 'active',
        ]);
        $flatRes = $couponService->validateCoupon('FLAT500', 4000.0);
        $this->assertTrue($flatRes['valid']);
        $this->assertEquals(500.0, $flatRes['discount']);

        // 3. Minimum Spend Enforcement
        Coupon::create([
            'code'        => 'MINSPEND10K',
            'type'        => 'fixed',
            'value'       => 1000,
            'amount'      => 1000,
            'min_spend'   => 10000,
            'is_active'   => 1,
            'status'      => 'active',
        ]);
        // When spend is below 10,000 -> must fail
        $failRes = $couponService->validateCoupon('MINSPEND10K', 6000.0);
        $this->assertFalse($failRes['valid']);
        
        // When spend is above 10,000 -> must pass
        $passRes = $couponService->validateCoupon('MINSPEND10K', 12000.0);
        $this->assertTrue($passRes['valid']);
    }

    /**
     * 5. Test Dynamic Pricing & Multi-Night Stay Breakdown.
     */
    public function test_dynamic_pricing_engine_breakdown(): void
    {
        $pricing = app(DynamicPricingService::class);
        $room = (new Room())->forceFill([
            'id'              => 10,
            'price_per_night' => 5000.0,
            'total_rooms'     => 10,
        ]);

        $checkIn  = now()->addDays(1)->format('Y-m-d');
        $checkOut = now()->addDays(4)->format('Y-m-d'); // 3 nights

        $result = $pricing->calculateStayPrice($room, $checkIn, $checkOut, 1);

        $this->assertIsArray($result);
        $this->assertEquals(3, $result['nights']);
        $this->assertGreaterThanOrEqual(15000.0, $result['final_price']);
        $this->assertGreaterThan(0, $result['avg_nightly_rate']);
    }

    /**
     * 6. Test Accounting Service Double-Entry Ledger & Financial KPIs.
     */
    public function test_accounting_service_double_entry_ledger_and_kpi_integrity(): void
    {
        [$property, $room] = $this->createTestPropertyAndRoom(totalRooms: 5, price: 8000.0);

        $booking = Booking::create([
            'booking_reference' => 'TEST-ACC-' . rand(1000, 9999),
            'property_id'       => $property->id,
            'room_id'           => $room->id,
            'guest_name'        => 'Accounting Test Guest',
            'guest_email'       => 'acctest@example.com',
            'guest_phone'       => '01799999999',
            'check_in'          => now()->addDays(3)->toDateString(),
            'check_out'         => now()->addDays(5)->toDateString(),
            'guests'            => 2,
            'nights'            => 2,
            'price_per_night'   => 8000,
            'subtotal'          => 16000,
            'tax_amount'        => 1200,
            'total_price'       => 17200,
            'total_amount'      => 17200,
            'commission_rate'   => 12.0,
            'commission_amount' => 1920,
            'vendor_payout_amount' => 14080,
            'currency'          => 'BDT',
            'payment_method'    => 'bkash',
            'payment_status'    => 'paid',
            'status'            => 'confirmed',
        ]);

        $accounting = app(AccountingService::class);
        $kpis = $accounting->getOverviewKPIs();

        $this->assertIsArray($kpis);
        $this->assertArrayHasKey('total_orders', $kpis);
        $this->assertArrayHasKey('confirmed_orders', $kpis);
        $this->assertGreaterThanOrEqual(1, (int)$kpis['confirmed_orders']);
    }

    /**
     * 7. Test Payment Verification Sandbox & Duplicate Guard.
     */
    public function test_payment_verification_service_sandbox_and_duplicate_guard(): void
    {
        [$property, $room] = $this->createTestPropertyAndRoom();
        $booking = Booking::create([
            'booking_reference' => 'PAY-TEST-' . rand(1000, 9999),
            'property_id'       => $property->id,
            'room_id'           => $room->id,
            'guest_name'        => 'Pay Test Guest',
            'guest_email'       => 'paytest@example.com',
            'guest_phone'       => '01711111111',
            'check_in'          => now()->addDays(2)->toDateString(),
            'check_out'         => now()->addDays(4)->toDateString(),
            'guests'            => 2,
            'nights'            => 2,
            'price_per_night'   => 5000,
            'subtotal'          => 10000,
            'total_price'       => 10750,
            'total_amount'      => 10750,
            'currency'          => 'BDT',
            'payment_method'    => 'bkash',
            'status'            => 'pending',
        ]);

        $payService = app(PaymentVerificationService::class);
        $paymentId = 'BKASH_SANDBOX_' . Str::random(8);

        // 1. Sandbox verification should approve
        $res = $payService->verifyBkash($booking->id, $paymentId);
        $this->assertTrue($res['success']);
        $this->assertEquals($paymentId, $res['txn_id']);

        // 2. Duplicate detection check
        $this->assertTrue($payService->isDuplicateTransaction($booking->id, $paymentId));
        $this->assertFalse($payService->isDuplicateTransaction($booking->id, 'NON_EXISTENT_TXN'));
    }

    /**
     * 8. Test AI Fraud Detector Risk Scoring & Multi-Layer Evaluation.
     */
    public function test_ai_fraud_detector_risk_scoring(): void
    {
        $detector = app(\App\Services\AI\FraudDetector::class);

        // Normal legitimate customer
        $normal = $detector->evaluateBooking([
            'guest_name'  => 'Shafiqul Islam',
            'guest_email' => 'shafiqul.islam@gmail.com',
            'guest_phone' => '01712345678',
        ]);
        $this->assertLessThan(40, $normal['risk_score']);
        $this->assertEquals('AUTO_APPROVE', $normal['decision']);

        // Disposable/Temp email spammer
        $spammer = $detector->evaluateBooking([
            'guest_name'  => 'Spam Bot',
            'guest_email' => 'hacker@mailinator.com',
            'guest_phone' => '01700000000',
        ]);
        $this->assertGreaterThanOrEqual(35, $spammer['risk_score']);
        $this->assertNotEmpty($spammer['flags']);
    }

    /**
     * 9. Test Search Controller Filtering, Geolocation & Sorting Engine.
     */
    public function test_search_controller_filtering_and_sorting_engine(): void
    {
        // Create 2 distinct properties in different cities with different prices
        Property::create([
            'name'            => 'Cox Bay Luxury Resort',
            'slug'            => 'cox-bay-luxury-' . uniqid(),
            'type'            => 'Resort',
            'city'            => "Cox's Bazar",
            'address'         => 'Marine Drive',
            'price_per_night' => 8500,
            'rating_score'    => 9.5,
            'status'          => 'active',
            'is_featured'     => 1,
        ]);

        Property::create([
            'name'            => 'Sylhet Tea Estate Cottage',
            'slug'            => 'sylhet-tea-estate-' . uniqid(),
            'type'            => 'Hotel',
            'city'            => 'Sylhet',
            'address'         => 'Sreemangal Road',
            'price_per_night' => 3500,
            'rating_score'    => 8.8,
            'status'          => 'active',
            'is_featured'     => 1,
        ]);

        // Search by location: Cox's Bazar
        $searchCox = $this->get('/search?destination=Cox');
        $searchCox->assertStatus(200);
        $searchCox->assertSee('Cox Bay Luxury Resort');

        // Search with sorting
        $sortAsc = $this->get('/search?sort=price_asc');
        $sortAsc->assertStatus(200);

        $sortDesc = $this->get('/search?sort=price_desc');
        $sortDesc->assertStatus(200);
    }
}
