<?php

namespace Tests\Feature;

use App\Models\Cab;
use App\Models\CabBooking;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CabAndTransportFacilityTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotel;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Grand Palace Hotel & Resort',
            'slug' => 'grand-palace-cab',
            'code' => 'GPH-CAB-01',
        ]);

        $this->user = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Transport Manager',
            'email' => 'transport@grandpalace.com',
            'role' => 'HOTEL_OWNER',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    public function test_can_view_cab_list_and_add_new_cab(): void
    {
        $response = $this->actingAs($this->user)->get('/cab-list.html');
        $response->assertStatus(200);
        $response->assertSee('Cab List');

        $storeResponse = $this->actingAs($this->user)->post('/cabs', [
            'car_name' => 'Toyota Hiace Grand Cabin',
            'car_number' => 'DHAKA-METRO-CHA-55-4422',
            'car_type' => 'Microbus',
            'fuel_type' => 'Octane',
            'seat_capacity' => 14,
            'driver_name' => 'Faruk Hossain',
            'driver_phone' => '+8801711223344',
            'base_fare' => 3500.00,
        ]);

        $storeResponse->assertStatus(302);
        $this->assertDatabaseHas('cabs', [
            'hotel_id' => $this->hotel->id,
            'car_name' => 'Toyota Hiace Grand Cabin',
            'car_number' => 'DHAKA-METRO-CHA-55-4422',
        ]);
    }

    public function test_can_book_cab_and_post_charge_to_room_folio(): void
    {
        $cab = Cab::create([
            'hotel_id' => $this->hotel->id,
            'car_name' => 'Toyota Premio',
            'car_number' => 'DHAKA-GA-11-2233',
            'car_type' => 'Sedan',
            'fuel_type' => 'Petrol',
            'seat_capacity' => 4,
            'driver_name' => 'Rahim Driver',
            'base_fare' => 1500.00,
        ]);

        $roomType = RoomType::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Deluxe King',
            'code' => 'DLX-KG',
            'base_price' => 5000.00,
            'capacity' => 2,
        ]);

        $room = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $roomType->id,
            'room_number' => '305',
            'floor' => 3,
            'status' => 'OCCUPIED',
            'cleanliness' => 'CLEAN',
        ]);

        $guest = Guest::create([
            'hotel_id' => $this->hotel->id,
            'first_name' => 'Rafiqul',
            'last_name' => 'Islam',
            'phone' => '+8801712345678',
        ]);

        $reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'reservation_code' => 'RES-CAB-001',
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'room_type_id' => $roomType->id,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'adults' => 2,
            'status' => 'CHECKED_IN',
            'total_amount' => 10000.00,
        ]);

        $folio = Folio::create([
            'hotel_id' => $this->hotel->id,
            'reservation_id' => $reservation->id,
            'guest_id' => $guest->id,
            'folio_number' => 'FOL-CAB-001',
            'total_charges' => 10000.00,
            'total_payments' => 0.00,
            'balance' => 10000.00,
            'status' => 'OPEN',
        ]);

        $bookResponse = $this->actingAs($this->user)->post('/cab-booking', [
            'cab_id' => $cab->id,
            'customer_name' => 'Dr. Rafiqul Islam',
            'customer_phone' => '+8801712345678',
            'driver_name' => 'Rahim Driver',
            'transport_type' => 'Pick Up',
            'booking_time' => '10:00 AM',
            'fare_amount' => 1500.00,
            'reservation_id' => $reservation->id,
            'post_to_room' => 1,
        ]);

        $bookResponse->assertStatus(302);

        $this->assertDatabaseHas('cab_bookings', [
            'hotel_id' => $this->hotel->id,
            'cab_id' => $cab->id,
            'customer_name' => 'Dr. Rafiqul Islam',
            'fare_amount' => 1500.00,
            'payment_status' => 'CHARGED_TO_ROOM',
        ]);

        $this->assertDatabaseHas('folio_items', [
            'folio_id' => $folio->id,
            'item_type' => 'CAB_SERVICE',
            'total_amount' => 1500.00,
        ]);

        $this->assertEquals(11500.00, $folio->fresh()->total_charges);
        $this->assertEquals(11500.00, $folio->fresh()->balance);
    }

    public function test_mobile_app_can_fetch_cabs_and_book_via_rest_api(): void
    {
        $token = $this->user->createToken('mobile-test')->plainTextToken;

        $cab = Cab::create([
            'hotel_id' => $this->hotel->id,
            'car_name' => 'Honda Vezel Hybrid',
            'car_number' => 'DHAKA-METRO-GA-33-9900',
            'car_type' => 'SUV',
            'fuel_type' => 'Hybrid',
            'seat_capacity' => 5,
            'driver_name' => 'Zubair',
            'base_fare' => 2000.00,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/cabs');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.0.car_name', 'Honda Vezel Hybrid');

        $bookResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/cabs/book', [
                'cab_id' => $cab->id,
                'customer_name' => 'Ayesha Siddiqua',
                'customer_phone' => '+8801899001122',
                'transport_type' => 'Drop',
                'fare_amount' => 2000.00,
            ]);

        $bookResponse->assertStatus(201);
        $bookResponse->assertJsonPath('success', true);
        $this->assertDatabaseHas('cab_bookings', [
            'hotel_id' => $this->hotel->id,
            'customer_name' => 'Ayesha Siddiqua',
        ]);
    }
}
