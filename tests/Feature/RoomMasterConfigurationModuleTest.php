<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomMasterConfigurationModuleTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotel;
    private User $admin;
    private RoomType $roomType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Grand Palace Hotel & Resort',
            'slug' => 'grand-palace-cfg',
            'code' => 'GPH-CFG-01',
        ]);

        $this->admin = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'General Manager',
            'email' => 'gm@grandpalace.com',
            'role' => 'HOTEL_OWNER',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->roomType = RoomType::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Deluxe King Suite',
            'code' => 'DLX-KNG',
            'base_price' => 12000.00,
            'capacity' => 2,
        ]);
    }

    public function test_can_manage_promocodes(): void
    {
        $response = $this->actingAs($this->admin)->get('/promocode-list.html');
        $response->assertStatus(200);
        $response->assertSee('Promocode List');

        $storeResponse = $this->actingAs($this->admin)->post('/room/promocodes', [
            'code' => 'VIPDISCOUNT1000',
            'room_type_id' => $this->roomType->id,
            'discount_value' => 1000.00,
            'valid_from' => now()->toDateString(),
            'valid_to' => now()->addMonths(3)->toDateString(),
        ]);

        $storeResponse->assertStatus(302);
        $this->assertDatabaseHas('promocodes', [
            'hotel_id' => $this->hotel->id,
            'code' => 'VIPDISCOUNT1000',
            'discount_value' => 1000.00,
        ]);
    }

    public function test_can_manage_room_facilities(): void
    {
        $response = $this->actingAs($this->admin)->get('/room-facilites-list.html');
        $response->assertStatus(200);
        $response->assertSee('Room Facilites List');

        $storeResponse = $this->actingAs($this->admin)->post('/room/facilities', [
            'name' => 'High-Speed Fiber WiFi 6',
            'description' => 'Dedicated 1Gbps internet per room',
        ]);

        $storeResponse->assertStatus(302);
        $this->assertDatabaseHas('room_facilities', [
            'hotel_id' => $this->hotel->id,
            'name' => 'High-Speed Fiber WiFi 6',
        ]);
    }

    public function test_can_manage_beds_and_sizes(): void
    {
        $bedResponse = $this->actingAs($this->admin)->post('/room/beds', [
            'name' => 'California Super King',
            'capacity' => 3,
        ]);
        $bedResponse->assertStatus(302);
        $this->assertDatabaseHas('bed_types', [
            'hotel_id' => $this->hotel->id,
            'name' => 'California Super King',
            'capacity' => 3,
        ]);

        $sizeResponse = $this->actingAs($this->admin)->post('/room/sizes', [
            'title' => 'Executive 650 Sq Ft',
            'size_sqft' => 650.00,
        ]);
        $sizeResponse->assertStatus(302);
        $this->assertDatabaseHas('room_sizes', [
            'hotel_id' => $this->hotel->id,
            'title' => 'Executive 650 Sq Ft',
            'size_sqft' => 650.00,
        ]);
    }

    public function test_can_manage_booking_types_commissions_and_complementary(): void
    {
        $bType = $this->actingAs($this->admin)->post('/room/booking-types', [
            'name' => 'Corporate Partner Booking',
            'channel' => 'B2B',
        ]);
        $bType->assertStatus(302);
        $this->assertDatabaseHas('booking_types', ['hotel_id' => $this->hotel->id, 'name' => 'Corporate Partner Booking']);

        $comm = $this->actingAs($this->admin)->post('/room/commissions', [
            'agent_name' => 'Agoda Partner Network',
            'rate_percentage' => 14.50,
            'channel' => 'OTA',
        ]);
        $comm->assertStatus(302);
        $this->assertDatabaseHas('commissions', ['hotel_id' => $this->hotel->id, 'agent_name' => 'Agoda Partner Network']);

        $comp = $this->actingAs($this->admin)->post('/room/complementary', [
            'title' => 'Complimentary Welcome Drink & Fruit Basket',
            'description' => 'Served upon guest check-in',
        ]);
        $comp->assertStatus(302);
        $this->assertDatabaseHas('complementary_services', ['hotel_id' => $this->hotel->id, 'title' => 'Complimentary Welcome Drink & Fruit Basket']);
    }
}
