<?php

namespace Tests\Feature;

use App\Models\Folio;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MicroscopicAllPagesAuditTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotel;
    private User $admin;
    private Reservation $reservation;
    private Folio $folio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Royal Microscopic Palace',
            'slug' => 'royal-microscopic-palace',
            'code' => 'RMP-01',
            'email' => 'concierge@microscopic.com',
            'phone' => '+1 800-555-0100',
            'address' => '100 Silicon Way, Tech City',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Dr. Microscopic Auditor',
            'email' => 'auditor@microscopic.com',
            'role' => 'SUPERADMIN',
            'password' => bcrypt('secret123456'),
            'is_active' => true,
        ]);

        $roomType = RoomType::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Royal Penthouse Suite',
            'code' => 'RPS-01',
            'base_price' => 1500.00,
            'max_occupancy' => 4,
        ]);

        $room = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $roomType->id,
            'room_number' => '101',
            'status' => 'AVAILABLE',
            'is_active' => true,
        ]);

        $guest = Guest::create([
            'hotel_id' => $this->hotel->id,
            'first_name' => 'Alan',
            'last_name' => 'Turing',
            'email' => 'turing@enigma.org',
            'phone' => '+44 700-000-001',
        ]);

        $this->reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'room_type_id' => $roomType->id,
            'reservation_code' => 'RES-ENIGMA-01',
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(5)->toDateString(),
            'status' => 'CONFIRMED',
            'source' => 'DIRECT_WALKIN',
            'total_amount' => 7500.00,
            'paid_amount' => 7500.00,
        ]);

        $this->folio = Folio::create([
            'hotel_id' => $this->hotel->id,
            'reservation_id' => $this->reservation->id,
            'folio_number' => 'FOL-ENIGMA-01',
            'status' => 'OPEN',
            'total_charges' => 7500.00,
            'total_payments' => 7500.00,
            'balance' => 0.00,
        ]);
    }

    public function test_microscopic_audit_all_public_and_auth_pages(): void
    {
        $publicUrls = [
            '/login',
            '/signin.html',
            '/signup.html',
            '/password-reset.html',
            '/two-step.html',
            '/lockscreen.html',
            '/maintenance.html',
            '/404.html',
        ];

        foreach ($publicUrls as $url) {
            $response = $this->get($url);
            $this->assertContains(
                $response->getStatusCode(),
                [200, 302],
                "Failed asserting HTTP status for public URL: {$url}"
            );
        }
    }

    public function test_microscopic_audit_all_authenticated_app_and_hotel_pages(): void
    {
        $authUrls = [
            '/',
            '/dashboard',
            '/index.html',
            '/index-two.html',
            '/hotel-dashboard.html',
            '/index-hr.html',
            '/hotels.html',
            '/tuning.html',
            '/search.html',
            '/search.html?q=Turing',
            '/common-settings.html',
            '/room-booking-list.html',
            '/room-list.html',
            '/room-status.html',
            '/floor-plan-list.html',
            '/bed-list.html',
            '/room-size.html',
            '/booking-type-list.html',
            '/commission-list.html',
            '/complementary-list.html',
            '/promocode-list.html',
            '/room-image.html',
            '/room-facilites-list.html',
            '/facilites-details.html',
            '/employees-all.html',
            '/employees-add.html',
            '/employees-departments.html',
            '/employees-attendance.html',
            '/duty-shift-list.html',
            '/duty-roster-list.html',
            '/duty-assigned-list.html',
            '/employees-leave.html',
            '/payroll-employee-salary.html',
            '/payroll-report.html',
            '/items-list.html',
            '/items-category-list.html',
            '/items-unit-list.html',
            '/items-suppliers-list.html',
            '/items-destroyed-list.html',
            '/stock-report.html',
            '/purchase-report.html',
            '/cab-list.html',
            '/cab-booking.html',
            '/personalised-spa.html',
            '/personalised-swimmingpool.html',
            '/personalised-jacuzzi.html',
            '/personalised-banquet.html',
            '/personalised-theater.html',
            '/personalised-pedicure.html',
            '/personalised-settings.html',
            '/housekeeping-assign-room.html',
            '/housekeeping-roomcleaning.html',
            '/housekeeping-Checklist.html',
            '/housekeeping-qrlist.html',
            '/housekeeping-report.html',
            '/transaction.html',
            '/booking-report.html',
            '/payment-set.html',
            '/role-list.html',
            '/role-add.html',
            '/role-permission.html',
            '/role-create-list.html',
            '/role-assign.html',
            '/role-access-list.html',
            '/calendar.html',
            '/inbox.html',
            '/chat.html',
            '/blog.html',
            '/filemanager.html',
            '/my-profile.html',
            '/sync-hub.html',
            '/about-us.html',
            '/contact-us.html',
            '/privacy-policy.html',
            '/terms-conditions.html',
            '/changelog.html',
            "/folio-invoice/{$this->folio->id}",
        ];

        foreach ($authUrls as $url) {
            $response = $this->actingAs($this->admin)->get($url);
            $this->assertEquals(
                200,
                $response->getStatusCode(),
                "Failed asserting 200 OK for Authenticated URL: {$url}"
            );
            $this->assertNotEmpty($response->getContent(), "Empty content returned for: {$url}");
        }
    }
}
