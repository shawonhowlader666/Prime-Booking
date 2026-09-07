<?php

namespace Tests\Feature;

use App\Models\Folio;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\NightAuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NightAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_night_audit_posts_room_tariff_tax_and_service_charge(): void
    {
        $hotel = Hotel::create([
            'name' => 'Seaside Resort',
            'slug' => 'seaside-resort',
            'code' => 'SSR-01',
            'tax_percentage' => 10.00,
            'service_charge_percentage' => 5.00,
            'is_active' => true,
        ]);

        $roomType = RoomType::create([
            'hotel_id' => $hotel->id,
            'name' => 'Ocean View',
            'code' => 'OV-1',
            'base_price' => 2000.00,
        ]);

        $room = Room::create([
            'hotel_id' => $hotel->id,
            'room_type_id' => $roomType->id,
            'room_number' => '301',
            'status' => 'OCCUPIED',
        ]);

        $guest = Guest::create([
            'hotel_id' => $hotel->id,
            'first_name' => 'Rahim',
            'last_name' => 'Uddin',
            'phone' => '01711000000',
        ]);

        $res = Reservation::create([
            'hotel_id' => $hotel->id,
            'reservation_code' => 'RES-TEST-001',
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'room_type_id' => $roomType->id,
            'check_in_date' => '2026-09-01',
            'check_out_date' => '2026-09-05',
            'status' => 'CHECKED_IN',
            'total_amount' => 0.00,
        ]);

        $folio = Folio::create([
            'hotel_id' => $hotel->id,
            'reservation_id' => $res->id,
            'folio_number' => 'FOL-RES-TEST-001',
            'status' => 'OPEN',
        ]);

        $nightAuditService = app(NightAuditService::class);
        $summary = $nightAuditService->runAudit($hotel, '2026-09-02');

        $this->assertEquals(1, $summary['in_house_count']);
        $this->assertEquals(1, $summary['room_charges_posted']);
        $this->assertEquals(200.00, $summary['total_tax_posted']); // 10% of 2000
        $this->assertEquals(100.00, $summary['total_service_charge_posted']); // 5% of 2000

        $folio->refresh();
        $this->assertEquals(2300.00, (float) $folio->total_charges);
        $this->assertEquals(2300.00, (float) $folio->balance);
    }
}
