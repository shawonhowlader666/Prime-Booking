<?php

namespace Tests\Feature;

use App\Models\Folio;
use App\Models\FolioItem;
use App\Models\Payment;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Services\NightAuditService;
use App\Services\PricingService;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UltraHighScaleStressAndFinancialAuditTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotelA;
    private Hotel $hotelB;
    private RoomType $deluxeSuiteA;
    private Room $room101A;
    private User $adminA;

    protected function setUp(): void
    {
        parent::setUp();

        // Hotel A
        $this->hotelA = Hotel::create([
            'name' => 'The Grand Azure Dhaka',
            'code' => 'AZURE-01',
            'slug' => 'the-grand-azure-dhaka',
            'tax_percentage' => 15.00,
            'service_charge_percentage' => 5.00,
            'currency' => 'BDT',
            'is_active' => true,
        ]);

        // Hotel B (Tenant Isolation verification)
        $this->hotelB = Hotel::create([
            'name' => 'Royal Palace Coxs Bazar',
            'code' => 'ROYAL-02',
            'slug' => 'royal-palace-coxs-bazar',
            'tax_percentage' => 10.00,
            'service_charge_percentage' => 5.00,
            'currency' => 'BDT',
            'is_active' => true,
        ]);

        $this->deluxeSuiteA = RoomType::create([
            'hotel_id' => $this->hotelA->id,
            'name' => 'Deluxe King Suite',
            'code' => 'DKS-100',
            'base_price' => 10000.00,
            'is_active' => true,
        ]);

        $this->room101A = Room::create([
            'hotel_id' => $this->hotelA->id,
            'room_type_id' => $this->deluxeSuiteA->id,
            'room_number' => '101',
            'floor' => '1',
            'status' => 'AVAILABLE',
            'is_active' => true,
        ]);

        $this->adminA = User::create([
            'hotel_id' => $this->hotelA->id,
            'name' => 'Azure General Manager',
            'email' => 'gm@grandazure.com',
            'password' => bcrypt('password'),
            'role' => 'SUPERADMIN',
            'is_active' => true,
        ]);
    }

    /**
     * 1. Concurrency & Overlapping Race Condition Test
     */
    public function test_concurrency_blocks_overlapping_reservations_on_same_room(): void
    {
        $reservationService = app(ReservationService::class);

        // Booking 1: Oct 10 to Oct 15
        $res1 = $reservationService->createReservation([
            'hotel_id' => $this->hotelA->id,
            'room_id' => $this->room101A->id,
            'room_type_id' => $this->deluxeSuiteA->id,
            'check_in_date' => '2026-10-10',
            'check_out_date' => '2026-10-15',
            'guest_first_name' => 'Tanvir',
            'guest_last_name' => 'Hasan',
            'total_amount' => 50000.00,
            'paid_amount' => 10000.00,
        ], $this->adminA);

        $this->assertNotNull($res1->id);
        $this->assertEquals('CONFIRMED', $res1->status);

        // Booking 2: Attempt overlapping booking on same room (Oct 12 to Oct 18) - MUST THROW EXCEPTION
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/already booked/i');

        $reservationService->createReservation([
            'hotel_id' => $this->hotelA->id,
            'room_id' => $this->room101A->id,
            'room_type_id' => $this->deluxeSuiteA->id,
            'check_in_date' => '2026-10-12',
            'check_out_date' => '2026-10-18',
            'guest_first_name' => 'Rafiq',
            'guest_last_name' => 'Islam',
            'total_amount' => 60000.00,
            'paid_amount' => 0.00,
        ], $this->adminA);
    }

    /**
     * 2. Financial Double-Entry Folio Integrity Test
     */
    public function test_financial_folio_double_entry_balance_exactness(): void
    {
        $guest = Guest::create([
            'hotel_id' => $this->hotelA->id,
            'first_name' => 'Ayesha',
            'last_name' => 'Siddiqua',
            'email' => 'ayesha@example.com',
        ]);

        $reservation = Reservation::create([
            'hotel_id' => $this->hotelA->id,
            'reservation_code' => 'RES-FIN-001',
            'guest_id' => $guest->id,
            'room_id' => $this->room101A->id,
            'room_type_id' => $this->deluxeSuiteA->id,
            'check_in_date' => '2026-11-01',
            'check_out_date' => '2026-11-04', // 3 nights
            'total_amount' => 30000.00,
            'paid_amount' => 0.00,
            'balance_amount' => 30000.00,
            'status' => 'CHECKED_IN',
        ]);

        $folio = Folio::create([
            'hotel_id' => $this->hotelA->id,
            'reservation_id' => $reservation->id,
            'folio_number' => 'FOL-FIN-001',
            'total_charges' => 0.00,
            'total_payments' => 0.00,
            'balance' => 0.00,
            'status' => 'OPEN',
        ]);

        // Post Room Tariff: 30,000
        FolioItem::create([
            'hotel_id' => $this->hotelA->id,
            'folio_id' => $folio->id,
            'item_type' => 'ROOM_CHARGE',
            'description' => '3 Nights Accommodation',
            'unit_price' => 10000.00,
            'quantity' => 3,
            'total_amount' => 30000.00,
            'posted_at' => now(),
        ]);

        // Post Restaurant Charge: 4,500
        FolioItem::create([
            'hotel_id' => $this->hotelA->id,
            'folio_id' => $folio->id,
            'item_type' => 'RESTAURANT',
            'description' => 'Fine Dining Dinner & Drinks',
            'unit_price' => 4500.00,
            'quantity' => 1,
            'total_amount' => 4500.00,
            'posted_at' => now(),
        ]);

        // Post Spa Service: 2,500
        FolioItem::create([
            'hotel_id' => $this->hotelA->id,
            'folio_id' => $folio->id,
            'item_type' => 'AMENITY_SERVICE',
            'description' => 'Aromatherapy Massage',
            'unit_price' => 2500.00,
            'quantity' => 1,
            'total_amount' => 2500.00,
            'posted_at' => now(),
        ]);

        // Total Charges = 30000 + 4500 + 2500 = 37,000
        $totalCharges = FolioItem::where('folio_id', $folio->id)->sum('total_amount');
        $this->assertEquals(37000.00, (float)$totalCharges);

        // Record Partial Payment 1: 20,000 via Card
        Payment::create([
            'hotel_id' => $this->hotelA->id,
            'folio_id' => $folio->id,
            'reservation_id' => $reservation->id,
            'payment_method' => 'CARD',
            'amount' => 20000.00,
            'status' => 'SUCCESS',
            'transaction_reference' => 'TXN-CARD-991',
        ]);

        // Record Partial Payment 2: 17,000 via Cash
        Payment::create([
            'hotel_id' => $this->hotelA->id,
            'folio_id' => $folio->id,
            'reservation_id' => $reservation->id,
            'payment_method' => 'CASH',
            'amount' => 17000.00,
            'status' => 'SUCCESS',
            'transaction_reference' => 'TXN-CASH-992',
        ]);

        $totalPayments = Payment::where('folio_id', $folio->id)->sum('amount');
        $this->assertEquals(37000.00, (float)$totalPayments);

        $netBalance = $totalCharges - $totalPayments;
        $this->assertEquals(0.00, (float)$netBalance);

        $folio->update([
            'total_charges' => $totalCharges,
            'total_payments' => $totalPayments,
            'balance' => $netBalance,
            'status' => 'SETTLED',
        ]);

        $this->assertEquals('SETTLED', $folio->fresh()->status);
        $this->assertEquals(0.00, (float)$folio->fresh()->balance);
    }

    /**
     * 3. Multi-Tenant Cross-Contamination Zero Tolerance Test
     */
    public function test_multi_tenant_cross_contamination_is_zero(): void
    {
        // Create Room in Hotel B
        $typeB = RoomType::create([
            'hotel_id' => $this->hotelB->id,
            'name' => 'Ocean View Villa',
            'code' => 'OVV-200',
            'base_price' => 25000.00,
            'is_active' => true,
        ]);

        $roomB = Room::create([
            'hotel_id' => $this->hotelB->id,
            'room_type_id' => $typeB->id,
            'room_number' => 'VILLA-01',
            'status' => 'AVAILABLE',
            'is_active' => true,
        ]);

        // Querying Hotel A must NEVER return Hotel B records
        $hotelARooms = Room::where('hotel_id', $this->hotelA->id)->pluck('id');
        $this->assertFalse($hotelARooms->contains($roomB->id));

        $hotelBRooms = Room::where('hotel_id', $this->hotelB->id)->pluck('id');
        $this->assertFalse($hotelBRooms->contains($this->room101A->id));
        $this->assertTrue($hotelBRooms->contains($roomB->id));
    }
}
