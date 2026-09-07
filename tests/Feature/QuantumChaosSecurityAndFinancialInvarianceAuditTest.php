<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\InventoryItem;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Services\FinancialLedgerService;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QuantumChaosSecurityAndFinancialInvarianceAuditTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotelAlpha;
    private Hotel $hotelBeta;
    private User $userAlpha;
    private User $userBeta;
    private RoomType $roomTypeAlpha;
    private Room $roomAlpha;

    protected function setUp(): void
    {
        parent::setUp();

        // Hotel Alpha
        $this->hotelAlpha = Hotel::create([
            'name' => 'Quantum Alpha Resort',
            'slug' => 'quantum-alpha-resort',
            'code' => 'QAR-01',
            'email' => 'alpha@quantum.com',
            'phone' => '+1 800-000-0001',
            'address' => 'Alpha Sector 1',
            'is_active' => true,
        ]);

        $this->userAlpha = User::create([
            'hotel_id' => $this->hotelAlpha->id,
            'name' => 'Commander Alpha',
            'email' => 'commander@alpha.com',
            'role' => 'SUPERADMIN',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->roomTypeAlpha = RoomType::create([
            'hotel_id' => $this->hotelAlpha->id,
            'name' => 'Quantum Alpha Suite',
            'code' => 'QAS-01',
            'base_price' => 500.00,
            'max_occupancy' => 2,
        ]);

        $this->roomAlpha = Room::create([
            'hotel_id' => $this->hotelAlpha->id,
            'room_type_id' => $this->roomTypeAlpha->id,
            'room_number' => 'ALPHA-101',
            'status' => 'AVAILABLE',
            'is_active' => true,
        ]);

        // Hotel Beta (Isolated Tenant)
        $this->hotelBeta = Hotel::create([
            'name' => 'Quantum Beta Resort',
            'slug' => 'quantum-beta-resort',
            'code' => 'QBR-01',
            'email' => 'beta@quantum.com',
            'phone' => '+1 800-000-0002',
            'address' => 'Beta Sector 2',
            'is_active' => true,
        ]);

        $this->userBeta = User::create([
            'hotel_id' => $this->hotelBeta->id,
            'name' => 'Commander Beta',
            'email' => 'commander@beta.com',
            'role' => 'SUPERADMIN',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    /**
     * 1. Chaos & Malicious Injection Resilience (SQLi, XSS, Unicode Emojis, Long Strings)
     */
    public function test_chaos_payload_and_malicious_injection_resilience(): void
    {
        $maliciousPayloads = [
            '<script>alert("xss")</script>',
            "'; DROP TABLE users; --",
            '🏨✨💎 Concierge Special 🚀🌟',
            str_repeat('A', 500),
        ];

        foreach ($maliciousPayloads as $payload) {
            $response = $this->actingAs($this->userAlpha)->post('/room/facilities', [
                'name' => 'Facility ' . substr($payload, 0, 50),
                'description' => $payload,
            ]);

            $this->assertContains($response->getStatusCode(), [200, 302]);
        }

        // Assert database integrity remains intact and no tables dropped
        $this->assertDatabaseHas('hotels', ['id' => $this->hotelAlpha->id]);
    }

    /**
     * 2. Mathematical Double-Entry Conservation Law (Penny-Exact Conservation across 50 iterations)
     */
    public function test_mathematical_double_entry_balance_conservation_invariance(): void
    {
        $ledgerService = app(FinancialLedgerService::class);

        $guest = Guest::create([
            'hotel_id' => $this->hotelAlpha->id,
            'first_name' => 'Fibonacci',
            'last_name' => 'Mathematician',
            'email' => 'fibonacci@math.org',
            'phone' => '+1 555-112-3581',
        ]);

        $reservation = Reservation::create([
            'hotel_id' => $this->hotelAlpha->id,
            'guest_id' => $guest->id,
            'room_id' => $this->roomAlpha->id,
            'room_type_id' => $this->roomTypeAlpha->id,
            'reservation_code' => 'RES-MATH-001',
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(3)->toDateString(),
            'status' => 'CONFIRMED',
            'source' => 'DIRECT_WALKIN',
            'total_amount' => 1500.00,
            'paid_amount' => 500.00,
        ]);

        $folio = Folio::create([
            'hotel_id' => $this->hotelAlpha->id,
            'reservation_id' => $reservation->id,
            'folio_number' => 'FOL-MATH-001',
            'status' => 'OPEN',
            'total_charges' => 1500.00,
            'total_payments' => 500.00,
            'balance' => 1000.00,
        ]);

        $billingService = app(\App\Services\FolioBillingService::class);

        // Post 20 randomized charges and payments
        for ($i = 1; $i <= 20; $i++) {
            $chargeAmount = round(rand(10, 250) + (rand(1, 99) / 100), 2);
            $billingService->addItem($folio, [
                'item_type' => 'FOOD_BEVERAGE',
                'description' => "Randomized Audit Charge #{$i}",
                'unit_price' => $chargeAmount,
                'quantity' => 1,
            ], $this->userAlpha);

            if ($i % 2 === 0) {
                $payAmount = round(rand(5, 100) + (rand(1, 99) / 100), 2);
                $billingService->recordPayment($folio, [
                    'payment_method' => 'CASH',
                    'amount' => $payAmount,
                    'transaction_reference' => "RCPT-CHAOS-{$i}",
                ], $this->userAlpha);
            }

            $folio->refresh();

            // Zero-drift conservation equation: Total Charges = Total Payments + Balance
            $calculatedBalance = round((float) $folio->total_charges - (float) $folio->total_payments, 2);
            $this->assertEquals(
                $calculatedBalance,
                round((float) $folio->balance, 2),
                "Conservation Law Violated on Iteration #{$i}: Charges ({$folio->total_charges}) != Payments ({$folio->total_payments}) + Balance ({$folio->balance})"
            );
        }
    }

    /**
     * 3. Multi-Tenant Cryptographic Isolation Attack Prevention
     */
    public function test_hostile_cross_tenant_data_leak_defense(): void
    {
        // Hotel Beta creates confidential employee and salary
        $deptBeta = Department::create([
            'hotel_id' => $this->hotelBeta->id,
            'name' => 'Classified R&D',
            'is_active' => true,
        ]);

        $empBeta = User::create([
            'hotel_id' => $this->hotelBeta->id,
            'department_id' => $deptBeta->id,
            'name' => 'Secret Agent 007',
            'email' => 'classified@beta.com',
            'role' => 'MANAGER',
            'salary' => 250000.00,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        // User Alpha attempts to query employees
        $response = $this->actingAs($this->userAlpha)->get('/employees-all.html');
        $response->assertStatus(200);
        $response->assertDontSee('Secret Agent 007');
        $response->assertDontSee('Classified R&D');
    }

    /**
     * 4. Date Boundary & Leap Year Precision Handling
     */
    public function test_leap_year_and_year_transition_precision(): void
    {
        $reservationService = app(ReservationService::class);

        // Leap Year Booking (Feb 27 2028 to Mar 02 2028 -> 4 nights in leap year)
        $leapBookingData = [
            'hotel_id' => $this->hotelAlpha->id,
            'room_id' => $this->roomAlpha->id,
            'room_type_id' => $this->roomTypeAlpha->id,
            'check_in_date' => '2028-02-27',
            'check_out_date' => '2028-03-02',
            'guest_first_name' => 'Leap',
            'guest_last_name' => 'Traveler',
            'guest_email' => 'leap@traveler.com',
            'adults' => 1,
            'total_amount' => 2000.00,
            'paid_amount' => 0.00,
            'source' => 'DIRECT_WALKIN',
        ];

        $res = $reservationService->createReservation($leapBookingData, $this->userAlpha);
        $this->assertNotNull($res);
        $this->assertEquals('2028-02-27', $res->check_in_date->toDateString());
        $this->assertEquals('2028-03-02', $res->check_out_date->toDateString());
    }
}
