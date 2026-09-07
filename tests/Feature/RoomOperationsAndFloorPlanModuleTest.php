<?php

namespace Tests\Feature;

use App\Models\FloorPlan;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomOperationsAndFloorPlanModuleTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotel;
    private User $admin;
    private RoomType $roomType;
    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Royal Room Operations Hotel',
            'slug' => 'royal-room-operations-hotel',
            'code' => 'RRO-01',
        ]);

        $this->admin = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'General Manager',
            'email' => 'gm@royalroomops.com',
            'role' => 'HOTEL_OWNER',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->roomType = RoomType::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Executive Royal Suite',
            'code' => 'EXEC-RS',
            'base_price' => 12000.00,
            'capacity' => 3,
        ]);

        $this->room = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $this->roomType->id,
            'room_number' => '801',
            'floor' => 8,
            'status' => 'AVAILABLE',
            'cleanliness' => 'CLEAN',
        ]);
    }

    public function test_can_view_and_create_rooms(): void
    {
        $response = $this->actingAs($this->admin)->get('/room-list.html');
        $response->assertStatus(200);
        $response->assertSee('Room List');
        $response->assertSee('801');

        $storeResponse = $this->actingAs($this->admin)->post('/rooms', [
            'room_number' => '802',
            'room_type_id' => $this->roomType->id,
            'floor' => 8,
            'capacity' => 2,
            'room_price' => 12000,
        ]);

        $storeResponse->assertStatus(302);
        $this->assertDatabaseHas('rooms', [
            'hotel_id' => $this->hotel->id,
            'room_number' => '802',
            'floor' => 8,
        ]);
    }

    public function test_can_view_room_bookings_and_room_status(): void
    {
        $guest = Guest::create([
            'hotel_id' => $this->hotel->id,
            'first_name' => 'Winston',
            'last_name' => 'Churchill',
            'email' => 'winston@pm.gov.uk',
            'phone' => '+442079460991',
        ]);

        $res = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'guest_id' => $guest->id,
            'room_id' => $this->room->id,
            'room_type_id' => $this->roomType->id,
            'reservation_code' => 'RES-ROYAL-8899',
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'adults' => 2,
            'children' => 0,
            'status' => 'CHECKED_IN',
            'source' => 'DIRECT_WALKIN',
            'total_amount' => 24000.00,
        ]);

        $bookingList = $this->actingAs($this->admin)->get('/room-booking-list.html');
        $bookingList->assertStatus(200);
        $bookingList->assertSee('Winston Churchill');

        $statusResponse = $this->actingAs($this->admin)->get('/room-status.html');
        $statusResponse->assertStatus(200);
        $statusResponse->assertSee('801');
    }

    public function test_can_manage_floor_plans(): void
    {
        $response = $this->actingAs($this->admin)->get('/floor-plan-list.html');
        $response->assertStatus(200);
        $response->assertSee('Floor Plan List');

        $storeFloor = $this->actingAs($this->admin)->post('/floor-plans', [
            'floor_name' => 'Eighth Floor Penthouse',
            'floor_number' => 8,
        ]);
        $storeFloor->assertStatus(302);
        $this->assertDatabaseHas('floor_plans', [
            'hotel_id' => $this->hotel->id,
            'floor_name' => 'Eighth Floor Penthouse',
            'floor_number' => 8,
        ]);

        $assignFloor = $this->actingAs($this->admin)->post('/floor-plans/assign', [
            'floor_name' => 'Eighth Floor Penthouse',
            'number_of_rooms' => 10,
            'start_room_number' => '800',
        ]);
        $assignFloor->assertStatus(302);
        $this->assertDatabaseHas('floor_plans', [
            'hotel_id' => $this->hotel->id,
            'floor_name' => 'Eighth Floor Penthouse',
            'number_of_rooms' => 10,
            'start_room_number' => '800',
        ]);
    }

    public function test_can_manage_room_images(): void
    {
        $response = $this->actingAs($this->admin)->get('/room-image.html');
        $response->assertStatus(200);
        $response->assertSee('Room Images');

        $storeImage = $this->actingAs($this->admin)->post('/room-images', [
            'room_type_id' => $this->roomType->id,
            'image_url' => 'assets/images/gallery/suite-presidential.jpg',
            'caption' => 'Executive Suite Living Area',
        ]);

        $storeImage->assertStatus(302);
        $this->assertDatabaseHas('room_images', [
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $this->roomType->id,
            'image_url' => 'assets/images/gallery/suite-presidential.jpg',
        ]);
    }

    public function test_can_view_checkin_out_details(): void
    {
        $response = $this->actingAs($this->admin)->get('/checkin-out.html?room_id=' . $this->room->id);
        $response->assertStatus(200);
        $response->assertSee('Checkin Out');
    }
}
