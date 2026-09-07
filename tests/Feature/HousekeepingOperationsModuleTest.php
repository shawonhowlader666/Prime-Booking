<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\HousekeepingChecklist;
use App\Models\HousekeepingTask;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HousekeepingOperationsModuleTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotel;
    private User $admin;
    private User $housekeeper;
    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Grand Palace Hotel & Resort',
            'slug' => 'grand-palace-hk',
            'code' => 'GPH-HK-01',
        ]);

        $this->admin = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Executive Housekeeper',
            'email' => 'hkadmin@grandpalace.com',
            'role' => 'HOTEL_OWNER',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->housekeeper = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Fatema Begum',
            'email' => 'fatema@grandpalace.com',
            'role' => 'HOUSEKEEPING',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $roomType = RoomType::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Executive Suite',
            'code' => 'EXE-STE',
            'base_price' => 8000.00,
            'capacity' => 2,
        ]);

        $this->room = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $roomType->id,
            'room_number' => '401',
            'floor' => 4,
            'status' => 'AVAILABLE',
            'cleanliness' => 'DIRTY',
        ]);
    }

    public function test_can_view_assign_room_and_assign_to_housekeeper(): void
    {
        $response = $this->actingAs($this->admin)->get('/housekeeping-assign-room.html');
        $response->assertStatus(200);
        $response->assertSee('Assign House Keeping');

        $assignResponse = $this->actingAs($this->admin)->post('/housekeeping/assign', [
            'assigned_to_user_id' => $this->housekeeper->id,
            'room_ids' => [$this->room->id],
            'priority' => 'HIGH',
        ]);

        $assignResponse->assertStatus(302);
        $this->assertDatabaseHas('housekeeping_tasks', [
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'assigned_to_user_id' => $this->housekeeper->id,
            'status' => 'IN_PROGRESS',
        ]);

        $this->assertEquals('CLEANING', $this->room->fresh()->cleanliness);
    }

    public function test_can_view_room_cleaning_and_complete_task(): void
    {
        $task = HousekeepingTask::create([
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'assigned_to_user_id' => $this->housekeeper->id,
            'priority' => 'HIGH',
            'status' => 'IN_PROGRESS',
        ]);

        $response = $this->actingAs($this->admin)->get('/housekeeping-roomcleaning.html');
        $response->assertStatus(200);
        $response->assertSee('Room Cleaning List');

        $completeResponse = $this->actingAs($this->admin)->post("/housekeeping/complete/{$task->id}");
        $completeResponse->assertStatus(302);

        $this->assertEquals('COMPLETED', $task->fresh()->status);
        $this->assertEquals('CLEAN', $this->room->fresh()->cleanliness);
    }

    public function test_can_manage_housekeeping_checklists(): void
    {
        $response = $this->actingAs($this->admin)->get('/housekeeping-Checklist.html');
        $response->assertStatus(200);
        $response->assertSee('Housekeeping Checklist');

        $storeResponse = $this->actingAs($this->admin)->post('/housekeeping/checklist', [
            'checkpoint' => 'Disinfect all door handles & TV remotes',
            'type' => 'House Keeper',
        ]);

        $storeResponse->assertStatus(302);
        $this->assertDatabaseHas('housekeeping_checklists', [
            'hotel_id' => $this->hotel->id,
            'checkpoint' => 'Disinfect all door handles & TV remotes',
            'type' => 'House Keeper',
        ]);
    }

    public function test_can_view_qr_list_and_staff_report(): void
    {
        $qrResponse = $this->actingAs($this->admin)->get('/housekeeping-qrlist.html');
        $qrResponse->assertStatus(200);
        $qrResponse->assertSee('House Keeping Qr-List');

        $reportResponse = $this->actingAs($this->admin)->get('/housekeeping-report.html');
        $reportResponse->assertStatus(200);
        $reportResponse->assertSee('Housekeeping Report');
    }
}
