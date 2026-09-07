<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\HousekeepingTask;
use App\Models\InventoryItem;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use App\Models\Payroll;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\TenantSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenancyTest extends TestCase
{
    use RefreshDatabase;

    protected Hotel $hotelA;
    protected Hotel $hotelB;
    protected User $userA;
    protected User $userB;
    protected RoomType $typeA;
    protected RoomType $typeB;
    protected Room $roomA;
    protected Room $roomB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotelA = Hotel::create([
            'name' => 'Grand Palace Hotel Alpha',
            'slug' => 'grand-palace-hotel-alpha',
            'code' => 'ALPHA-001',
            'email' => 'contact@alpha.com',
            'phone' => '+18000000001',
            'address' => 'Alpha Boulevard 1',
            'is_active' => true,
        ]);

        TenantSubscription::create([
            'hotel_id' => $this->hotelA->id,
            'plan_name' => 'Enterprise Plan',
            'amount' => 1000.00,
            'currency' => 'BDT',
            'status' => 'active',
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->addYear(),
        ]);

        $this->hotelB = Hotel::create([
            'name' => 'Sunset Resort Hotel Beta',
            'slug' => 'sunset-resort-hotel-beta',
            'code' => 'BETA-002',
            'email' => 'contact@beta.com',
            'phone' => '+18000000002',
            'address' => 'Beta Beach Road 2',
            'is_active' => true,
        ]);

        TenantSubscription::create([
            'hotel_id' => $this->hotelB->id,
            'plan_name' => 'Enterprise Plan',
            'amount' => 1000.00,
            'currency' => 'BDT',
            'status' => 'active',
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->addYear(),
        ]);

        $this->userA = User::create([
            'hotel_id' => $this->hotelA->id,
            'name' => 'Alpha General Manager',
            'email' => 'gm@alpha.com',
            'role' => 'MANAGER',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $this->userB = User::create([
            'hotel_id' => $this->hotelB->id,
            'name' => 'Beta General Manager',
            'email' => 'gm@beta.com',
            'role' => 'MANAGER',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $this->typeA = RoomType::create([
            'hotel_id' => $this->hotelA->id,
            'name' => 'Alpha Presidential Penthouse',
            'code' => 'A-PENT',
            'base_price' => 5000.00,
            'max_occupancy' => 4,
        ]);

        $this->typeB = RoomType::create([
            'hotel_id' => $this->hotelB->id,
            'name' => 'Beta Royal Beach Villa',
            'code' => 'B-VILLA',
            'base_price' => 8500.00,
            'max_occupancy' => 6,
        ]);

        $this->roomA = Room::create([
            'hotel_id' => $this->hotelA->id,
            'room_type_id' => $this->typeA->id,
            'room_number' => '101-ALPHA',
            'status' => 'AVAILABLE',
            'is_active' => true,
        ]);

        $this->roomB = Room::create([
            'hotel_id' => $this->hotelB->id,
            'room_type_id' => $this->typeB->id,
            'room_number' => '909-BETA',
            'status' => 'AVAILABLE',
            'is_active' => true,
        ]);
    }

    /**
     * 1. Test Strict Tenant Isolation on Rooms & Room Types
     */
    public function test_tenant_scope_isolates_room_and_room_types(): void
    {
        // User Alpha
        $this->actingAs($this->userA);
        $roomsA = Room::all();
        $this->assertCount(1, $roomsA);
        $this->assertEquals('101-ALPHA', $roomsA->first()->room_number);
        $this->assertEquals('Alpha Presidential Penthouse', RoomType::first()->name);

        // User Beta
        $this->actingAs($this->userB);
        $roomsB = Room::all();
        $this->assertCount(1, $roomsB);
        $this->assertEquals('909-BETA', $roomsB->first()->room_number);
        $this->assertEquals('Beta Royal Beach Villa', RoomType::first()->name);
    }

    /**
     * 2. Test Strict Tenant Isolation on Guests, Reservations & CRM
     */
    public function test_tenant_scope_isolates_guests_and_reservations(): void
    {
        $guestA = Guest::create([
            'hotel_id' => $this->hotelA->id,
            'first_name' => 'Alpha',
            'last_name' => 'VIP Client',
            'email' => 'vip@alpha.com',
            'phone' => '+15551111111',
        ]);

        $resA = Reservation::create([
            'hotel_id' => $this->hotelA->id,
            'guest_id' => $guestA->id,
            'room_type_id' => $this->typeA->id,
            'room_id' => $this->roomA->id,
            'reservation_code' => 'RES-ALPHA-777',
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'status' => 'CONFIRMED',
            'total_amount' => 10000.00,
            'source' => 'DIRECT',
        ]);

        $guestB = Guest::create([
            'hotel_id' => $this->hotelB->id,
            'first_name' => 'Beta',
            'last_name' => 'Celebrity Client',
            'email' => 'celebrity@beta.com',
            'phone' => '+15552222222',
        ]);

        $resB = Reservation::create([
            'hotel_id' => $this->hotelB->id,
            'guest_id' => $guestB->id,
            'room_type_id' => $this->typeB->id,
            'room_id' => $this->roomB->id,
            'reservation_code' => 'RES-BETA-888',
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(3)->toDateString(),
            'status' => 'CONFIRMED',
            'total_amount' => 25500.00,
            'source' => 'OTA',
        ]);

        // When User Alpha queries:
        $this->actingAs($this->userA);
        $this->assertCount(1, Guest::all());
        $this->assertEquals('Alpha VIP Client', Guest::first()->first_name . ' ' . Guest::first()->last_name);
        $this->assertCount(1, Reservation::all());
        $this->assertEquals('RES-ALPHA-777', Reservation::first()->reservation_code);

        // When User Beta queries:
        $this->actingAs($this->userB);
        $this->assertCount(1, Guest::all());
        $this->assertEquals('Beta Celebrity Client', Guest::first()->first_name . ' ' . Guest::first()->last_name);
        $this->assertCount(1, Reservation::all());
        $this->assertEquals('RES-BETA-888', Reservation::first()->reservation_code);
    }

    /**
     * 3. Test Strict Tenant Isolation on Financial Billing & Folios
     */
    public function test_tenant_scope_isolates_billing_folios(): void
    {
        $guestA = Guest::create([
            'hotel_id' => $this->hotelA->id,
            'first_name' => 'Alpha',
            'last_name' => 'Guest',
            'email' => 'guest@alpha.com',
            'phone' => '+15551111112',
        ]);

        $resA = Reservation::create([
            'hotel_id' => $this->hotelA->id,
            'guest_id' => $guestA->id,
            'room_type_id' => $this->typeA->id,
            'room_id' => $this->roomA->id,
            'reservation_code' => 'RES-A-FOL',
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'status' => 'CONFIRMED',
            'total_amount' => 5000.00,
            'source' => 'DIRECT',
        ]);

        $folioA = Folio::create([
            'hotel_id' => $this->hotelA->id,
            'reservation_id' => $resA->id,
            'folio_number' => 'FOL-ALPHA-01',
            'status' => 'OPEN',
            'total_charges' => 5000.00,
            'total_payments' => 5000.00,
            'balance' => 0.00,
        ]);

        $guestB = Guest::create([
            'hotel_id' => $this->hotelB->id,
            'first_name' => 'Beta',
            'last_name' => 'Guest',
            'email' => 'guest@beta.com',
            'phone' => '+15552222223',
        ]);

        $resB = Reservation::create([
            'hotel_id' => $this->hotelB->id,
            'guest_id' => $guestB->id,
            'room_type_id' => $this->typeB->id,
            'room_id' => $this->roomB->id,
            'reservation_code' => 'RES-B-FOL',
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(3)->toDateString(),
            'status' => 'CONFIRMED',
            'total_amount' => 12000.00,
            'source' => 'DIRECT',
        ]);

        $folioB = Folio::create([
            'hotel_id' => $this->hotelB->id,
            'reservation_id' => $resB->id,
            'folio_number' => 'FOL-BETA-02',
            'status' => 'OPEN',
            'total_charges' => 12000.00,
            'total_payments' => 6000.00,
            'balance' => 6000.00,
        ]);

        // User Alpha cannot see Folio B
        $this->actingAs($this->userA);
        $this->assertCount(1, Folio::all());
        $this->assertEquals('FOL-ALPHA-01', Folio::first()->folio_number);

        // User Beta cannot see Folio A
        $this->actingAs($this->userB);
        $this->assertCount(1, Folio::all());
        $this->assertEquals('FOL-BETA-02', Folio::first()->folio_number);
    }

    /**
     * 4. Test Cross-Tenant Hostile API Infiltration Prevention
     */
    public function test_cross_tenant_api_infiltration_defense(): void
    {
        $guestB = Guest::create([
            'hotel_id' => $this->hotelB->id,
            'first_name' => 'Secret',
            'last_name' => 'Guest',
            'email' => 'secret@beta.com',
            'phone' => '+15559998888',
        ]);

        $resB = Reservation::create([
            'hotel_id' => $this->hotelB->id,
            'guest_id' => $guestB->id,
            'room_type_id' => $this->typeB->id,
            'room_id' => $this->roomB->id,
            'reservation_code' => 'RES-CONFIDENTIAL-B',
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'status' => 'CONFIRMED',
            'total_amount' => 9999.00,
            'source' => 'DIRECT',
        ]);

        // User A tries to sneak into User B's reservation via API
        $this->actingAs($this->userA);
        $response = $this->getJson("/api/v1/reservations/{$resB->id}");
        $this->assertTrue(in_array($response->getStatusCode(), [404, 403]));

        // User A tries to list rooms via API
        $apiRooms = $this->getJson('/api/v1/rooms');
        $apiRooms->assertStatus(200);
        $roomsData = $apiRooms->json()['data'] ?? $apiRooms->json();
        $this->assertCount(1, $roomsData);
        $this->assertEquals('101-ALPHA', $roomsData[0]['room_number']);
    }
}
