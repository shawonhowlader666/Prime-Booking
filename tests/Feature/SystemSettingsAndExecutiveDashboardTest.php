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

class SystemSettingsAndExecutiveDashboardTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotel;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Grand Executive Hotel',
            'slug' => 'grand-executive-hotel',
            'code' => 'GEH-01',
            'email' => 'admin@geh.com',
            'phone' => '+1 555-987-6543',
            'address' => '100 Executive Boulevard',
        ]);

        $this->admin = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'General Manager',
            'email' => 'gm@geh.com',
            'role' => 'SUPERADMIN',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $roomType = RoomType::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Presidential Suite',
            'code' => 'PS-01',
            'base_price' => 750.00,
            'max_occupancy' => 4,
        ]);

        $room = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $roomType->id,
            'room_number' => '901',
            'status' => 'OCCUPIED',
            'is_active' => true,
        ]);

        $guest = Guest::create([
            'hotel_id' => $this->hotel->id,
            'first_name' => 'Alexander',
            'last_name' => 'Hamilton',
            'email' => 'alexander@hamilton.org',
            'phone' => '+1 555-001-9988',
        ]);

        Reservation::create([
            'hotel_id' => $this->hotel->id,
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'room_type_id' => $roomType->id,
            'reservation_code' => 'RES-990011',
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(3)->toDateString(),
            'status' => 'CONFIRMED',
            'source' => 'WALK_IN',
            'total_amount' => 2250.00,
        ]);
    }

    public function test_can_view_tuning_and_hotels_hub(): void
    {
        $tuningResp = $this->actingAs($this->admin)->get('/tuning.html');
        $tuningResp->assertStatus(200);
        $tuningResp->assertSee('Tuning');

        $hotelsResp = $this->actingAs($this->admin)->get('/hotels.html');
        $hotelsResp->assertStatus(200);
        $hotelsResp->assertSee('Hotels');
    }

    public function test_can_view_and_save_common_settings(): void
    {
        $settingsResp = $this->actingAs($this->admin)->get('/common-settings.html');
        $settingsResp->assertStatus(200);
        $settingsResp->assertSee('Common Settings');
        $settingsResp->assertSee('Grand Executive Hotel');

        $saveResp = $this->actingAs($this->admin)->post('/common-settings', [
            'name' => 'Grand Executive Resort & Spa',
            'email' => 'contact@grandresort.com',
            'phone' => '+1 555-777-8899',
            'address' => '200 Ocean View Avenue',
        ]);

        $saveResp->assertStatus(302);
        $this->assertEquals('Grand Executive Resort & Spa', $this->hotel->fresh()->name);
    }

    public function test_can_search_pms_records(): void
    {
        $searchResp = $this->actingAs($this->admin)->get('/search.html?q=Hamilton');
        $searchResp->assertStatus(200);
        $searchResp->assertSee('Alexander Hamilton');

        $resSearchResp = $this->actingAs($this->admin)->get('/search.html?q=RES-990011');
        $resSearchResp->assertStatus(200);
        $resSearchResp->assertSee('RES-990011');
    }

    public function test_can_view_executive_dashboard_two(): void
    {
        $dashTwoResp = $this->actingAs($this->admin)->get('/index-two.html');
        $dashTwoResp->assertStatus(200);
        $dashTwoResp->assertSee('Hotel Activity Stream');
        $dashTwoResp->assertSee('RES-990011');
    }

    public function test_can_view_all_auxiliary_and_static_pages(): void
    {
        $pages = [
            '/hotel-dashboard.html',
            '/index-hr.html',
            '/sync-hub.html',
            '/about-us.html',
            '/contact-us.html',
            '/privacy-policy.html',
            '/terms-conditions.html',
            '/changelog.html',
        ];

        foreach ($pages as $page) {
            $resp = $this->actingAs($this->admin)->get($page);
            $resp->assertStatus(200);
        }
    }
}
