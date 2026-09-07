<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelPmsCoreApiTest extends TestCase
{
    use RefreshDatabase;

    protected Hotel $hotel;
    protected User $user;
    protected RoomType $roomType;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Seaside Grand Resort',
            'slug' => 'seaside-grand-resort',
            'code' => 'SGR-01',
            'currency' => 'BDT',
            'sync_webhook_secret' => 'test_secret_123',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Admin User',
            'email' => 'admin@resort.com',
            'role' => 'HOTEL_OWNER',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->roomType = RoomType::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Deluxe Suite',
            'code' => 'DLX-01',
            'base_price' => 5000.00,
            'max_adults' => 2,
            'max_children' => 1,
            'is_active' => true,
        ]);

        $this->room = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $this->roomType->id,
            'room_number' => '101',
            'status' => 'AVAILABLE',
            'is_active' => true,
        ]);
    }

    public function test_user_can_login_via_sanctum_and_get_token(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@resort.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email', 'role'],
                    'hotel' => ['id', 'name', 'code'],
                ],
            ]);
    }

    public function test_can_fetch_dashboard_stats_and_kpis(): void
    {
        $token = $this->user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/dashboard/stats');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'kpis' => ['total_rooms', 'occupied_rooms', 'available_rooms', 'occupancy_rate'],
                    'recent_bookings',
                    'rooms',
                ],
            ]);
    }

    public function test_can_create_walkin_reservation_and_auto_generate_folio(): void
    {
        $token = $this->user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/reservations', [
                'room_id' => $this->room->id,
                'room_type_id' => $this->roomType->id,
                'check_in_date' => now()->toDateString(),
                'check_out_date' => now()->addDays(2)->toDateString(),
                'guest_first_name' => 'Jamal',
                'guest_last_name' => 'Hossain',
                'guest_phone' => '+8801700000000',
                'adults' => 2,
                'total_amount' => 10000.00,
                'paid_amount' => 5000.00,
                'source' => 'DIRECT_WALKIN',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total_amount', '10000.00')
            ->assertJsonPath('data.balance_amount', '5000.00');

        $this->assertDatabaseHas('reservations', [
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'total_amount' => 10000.00,
        ]);

        $this->assertDatabaseHas('folios', [
            'hotel_id' => $this->hotel->id,
            'total_charges' => 10000.00,
        ]);
    }

    public function test_can_check_in_and_check_out_guest(): void
    {
        $token = $this->user->createToken('test')->plainTextToken;

        $guest = Guest::create([
            'hotel_id' => $this->hotel->id,
            'first_name' => 'Anis',
            'last_name' => 'Zaman',
        ]);

        $reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'reservation_code' => 'RES-TEST-001',
            'guest_id' => $guest->id,
            'room_id' => $this->room->id,
            'room_type_id' => $this->roomType->id,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(1)->toDateString(),
            'status' => 'CONFIRMED',
            'total_amount' => 5000.00,
        ]);

        // 1. Check in
        $checkInRes = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/reservations/{$reservation->id}/check-in");

        $checkInRes->assertStatus(200)->assertJsonPath('data.status', 'CHECKED_IN');
        $this->assertEquals('OCCUPIED', $this->room->fresh()->status);

        // 2. Check out
        $checkOutRes = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/reservations/{$reservation->id}/check-out");

        $checkOutRes->assertStatus(200)->assertJsonPath('data.status', 'CHECKED_OUT');
        $this->assertEquals('DIRTY', $this->room->fresh()->status);
    }

    public function test_can_handle_incoming_booking_from_prime_booking_ota(): void
    {
        $payload = [
            'room_type_code' => $this->roomType->code,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(3)->toDateString(),
            'guest_first_name' => 'Online',
            'guest_last_name' => 'Booker',
            'guest_email' => 'ota@guest.com',
            'guest_phone' => '+8801999999999',
            'adults' => 2,
            'children' => 0,
            'total_amount' => 15000.00,
            'paid_amount' => 15000.00,
            'idempotency_key' => 'ota-idem-test-12345',
        ];

        $rawJson = json_encode($payload);
        $signature = hash_hmac('sha256', $rawJson, $this->hotel->sync_webhook_secret);

        $response = $this->call('POST', '/api/v1/sync/prime-booking/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_HOTEL_CODE' => $this->hotel->code,
            'HTTP_X_PRIME_SIGNATURE' => $signature,
        ], $rawJson);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'OTA booking successfully recorded in PMS');

        $this->assertDatabaseHas('reservations', [
            'hotel_id' => $this->hotel->id,
            'source' => 'PRIME_BOOKING_OTA',
            'total_amount' => 15000.00,
        ]);
    }
}
