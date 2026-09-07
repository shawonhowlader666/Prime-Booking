<?php

namespace Tests\Feature;

use App\Models\AmenityService;
use App\Models\Cab;
use App\Models\Department;
use App\Models\DutyRoster;
use App\Models\DutyShift;
use App\Models\Employee;
use App\Models\EmployeeLeave;
use App\Models\FloorPlan;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\InventoryItem;
use App\Models\ItemCategory;
use App\Models\ItemSupplier;
use App\Models\ItemUnit;
use App\Models\Payroll;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionMasterEndToEndUserJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_production_master_user_journey_from_scratch(): void
    {
        // --------------------------------------------------------------------------------
        // 1. User Registration & Dynamic Login as SuperAdmin Property Owner
        // --------------------------------------------------------------------------------
        $signupResp = $this->post('/signup', [
            'first_name' => 'Richard',
            'last_name' => 'Hendricks',
            'email' => 'richard@piedpiperhotel.com',
            'password' => 'secret123456',
            'password_confirmation' => 'secret123456',
        ]);
        $signupResp->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'richard@piedpiperhotel.com',
            'role' => 'SUPERADMIN',
        ]);

        $user = User::where('email', 'richard@piedpiperhotel.com')->first();
        $hotel = Hotel::find($user->hotel_id);

        // --------------------------------------------------------------------------------
        // 2. Hotel Property Settings Configuration
        // --------------------------------------------------------------------------------
        $settingsResp = $this->actingAs($user)->post('/common-settings', [
            'name' => 'Pied Piper Grand Luxury Resort',
            'email' => 'concierge@piedpiperhotel.com',
            'phone' => '+1 415-555-0199',
            'address' => '5230 Newell Road, Palo Alto, CA',
        ]);
        $settingsResp->assertStatus(302);
        $this->assertEquals('Pied Piper Grand Luxury Resort', $hotel->fresh()->name);

        $viewSettings = $this->actingAs($user)->get('/common-settings.html');
        $viewSettings->assertStatus(200);
        $viewSettings->assertSee('Pied Piper Grand Luxury Resort');

        // --------------------------------------------------------------------------------
        // 3. Room Master Configurations (Facilities, Beds, Sizes, Booking Types, Commissions, Promos)
        // --------------------------------------------------------------------------------
        $facilityResp = $this->actingAs($user)->post('/room/facilities', [
            'name' => 'High-Speed Fiber WiFi 1Gbps',
            'icon' => 'fa-wifi',
            'description' => 'Ultra high speed dedicated fiber line',
        ]);
        $facilityResp->assertStatus(302);
        $this->assertDatabaseHas('room_facilities', ['name' => 'High-Speed Fiber WiFi 1Gbps']);

        $bedResp = $this->actingAs($user)->post('/room/beds', [
            'name' => 'Californian Emperor Bed',
            'dimensions' => '210cm x 210cm',
        ]);
        $bedResp->assertStatus(302);
        $this->assertDatabaseHas('bed_types', ['name' => 'Californian Emperor Bed']);

        $sizeResp = $this->actingAs($user)->post('/room/sizes', [
            'title' => 'Executive Grand Penthouse',
            'size_sqft' => 850,
        ]);
        $sizeResp->assertStatus(302);
        $this->assertDatabaseHas('room_sizes', ['size_sqft' => 850]);

        $bTypeResp = $this->actingAs($user)->post('/room/booking-types', [
            'name' => 'Direct Corporate VIP',
            'channel' => 'Corporate Direct',
        ]);
        $bTypeResp->assertStatus(302);
        $this->assertDatabaseHas('booking_types', ['name' => 'Direct Corporate VIP']);

        $commResp = $this->actingAs($user)->post('/room/commissions', [
            'agent_name' => 'Silicon Valley Travel Concierge',
            'rate_percentage' => 10.00,
        ]);
        $commResp->assertStatus(302);
        $this->assertDatabaseHas('commissions', ['agent_name' => 'Silicon Valley Travel Concierge']);

        $promoResp = $this->actingAs($user)->post('/room/promocodes', [
            'code' => 'TECHSUMMIT2026',
            'discount_value' => 20.00,
            'valid_from' => now()->toDateString(),
            'valid_to' => now()->addMonths(6)->toDateString(),
        ]);
        $promoResp->assertStatus(302);
        $this->assertDatabaseHas('promocodes', ['code' => 'TECHSUMMIT2026']);

        // Floor Plan & Physical Room Setup
        $floorResp = $this->actingAs($user)->post('/floor-plans', [
            'floor_name' => 'Floor 10 - Executive Wing',
            'floor_number' => 10,
            'number_of_rooms' => 12,
        ]);
        $floorResp->assertStatus(302);
        $this->assertDatabaseHas('floor_plans', ['floor_number' => 10]);

        $roomType = RoomType::create([
            'hotel_id' => $hotel->id,
            'name' => 'Founder Presidential Penthouse',
            'code' => 'FNDR-PENT',
            'base_price' => 1200.00,
            'max_occupancy' => 4,
        ]);

        $roomResp = $this->actingAs($user)->post('/rooms', [
            'room_number' => '1001',
            'room_type_id' => $roomType->id,
            'floor' => 10,
            'status' => 'AVAILABLE',
            'cleanliness' => 'CLEAN',
        ]);
        $roomResp->assertStatus(302);
        $this->assertDatabaseHas('rooms', ['room_number' => '1001']);
        $room = Room::where('room_number', '1001')->first();

        $imageResp = $this->actingAs($user)->post('/room-images', [
            'room_type_id' => $roomType->id,
            'room_id' => $room->id,
            'image_url' => 'assets/images/gallery/penthouse-suite.jpg',
            'caption' => 'Penthouse Balcony Sunset View',
        ]);
        $imageResp->assertStatus(302);
        $this->assertDatabaseHas('room_images', ['caption' => 'Penthouse Balcony Sunset View']);

        // View Room Operations views and assert dynamic records render
        $viewRooms = $this->actingAs($user)->get('/room-list.html');
        $viewRooms->assertStatus(200);
        $viewRooms->assertSee('1001');

        $viewStatus = $this->actingAs($user)->get('/room-status.html');
        $viewStatus->assertStatus(200);
        $viewStatus->assertSee('1001');

        $viewFloors = $this->actingAs($user)->get('/floor-plan-list.html');
        $viewFloors->assertStatus(200);
        $viewFloors->assertSee('Floor 10 - Executive Wing');

        // --------------------------------------------------------------------------------
        // 4. Real Walk-in Reservation, Pricing Calculation & Folio Double-Entry
        // --------------------------------------------------------------------------------
        $walkInPayload = [
            'hotel_id' => $hotel->id,
            'guest_first_name' => 'Dinesh',
            'guest_last_name' => 'Chugtai',
            'guest_email' => 'dinesh@piedpiper.com',
            'guest_phone' => '+1 415-888-9900',
            'room_id' => $room->id,
            'room_type_id' => $roomType->id,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'adults' => 2,
            'children' => 0,
            'total_amount' => 2400.00,
            'paid_amount' => 1200.00,
            'payment_method' => 'STRIPE_CARD',
        ];

        $walkInResp = $this->actingAs($user)->postJson('/api/v1/reservations', $walkInPayload);
        $walkInResp->assertStatus(201);
        $reservationId = $walkInResp->json('data.id');
        $reservationCode = $walkInResp->json('data.reservation_code');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservationId,
            'reservation_code' => $reservationCode,
            'status' => 'CONFIRMED',
        ]);

        $folio = Folio::where('reservation_id', $reservationId)->first();
        $this->assertNotNull($folio);
        $this->assertEquals(2400.00, (float) $folio->total_charges);
        $this->assertEquals(1200.00, (float) $folio->total_payments);
        $this->assertEquals(1200.00, (float) $folio->balance);

        // Check in guest via API
        $checkInResp = $this->actingAs($user)->postJson("/api/v1/reservations/{$reservationId}/check-in");
        $checkInResp->assertStatus(200);
        $this->assertEquals('OCCUPIED', $room->fresh()->status);

        // View Folio Invoice & Print View
        $viewFolio = $this->actingAs($user)->get("/folio-invoice/{$folio->id}");
        $viewFolio->assertStatus(200);
        $viewFolio->assertSee($reservationCode);
        $viewFolio->assertSee('Dinesh Chugtai');
        $viewFolio->assertSee('1001');

        // --------------------------------------------------------------------------------
        // 5. Guest Amenities & Cab Dispatch with Dynamic Ledger Posting
        // --------------------------------------------------------------------------------
        $amenityServiceResp = $this->actingAs($user)->post('/amenities/services', [
            'category' => 'SPA',
            'name' => 'Silicon Valley Stress-Relief Aromatherapy',
            'price' => 350.00,
            'duration_minutes' => 90,
            'description' => 'Aromatherapy massage for tech executives',
        ]);
        $amenityServiceResp->assertStatus(302);
        $service = AmenityService::where('name', 'Silicon Valley Stress-Relief Aromatherapy')->first();

        // Book amenity and post to room folio
        $guest = Guest::where('email', 'dinesh@piedpiper.com')->first();
        $bookAmenityResp = $this->actingAs($user)->post('/amenities/book', [
            'service_id' => $service->id,
            'guest_id' => $guest->id,
            'reservation_id' => $reservationId,
            'room_id' => $room->id,
            'booked_for_date' => now()->toDateString(),
            'time_slot' => '14:00 - 15:30',
            'quantity' => 1,
            'unit_price' => 350.00,
        ]);
        $bookAmenityResp->assertStatus(302);

        // Register VIP Tesla Fleet Cab
        $cabResp = $this->actingAs($user)->post('/cabs', [
            'car_name' => 'Tesla Model S Plaid VIP',
            'car_number' => 'CAL-PIED-01',
            'car_type' => 'Sedan',
            'fuel_type' => 'EV',
            'seat_capacity' => 4,
            'driver_name' => 'Jared Dunn',
            'driver_phone' => '+1 415-999-0011',
            'base_fare' => 150.00,
            'status' => 'AVAILABLE',
        ]);
        $cabResp->assertStatus(302);
        $cab = Cab::where('car_number', 'CAL-PIED-01')->first();

        // Dispatch Cab Transfer for Room 1001
        $bookCabResp = $this->actingAs($user)->post('/cab-booking', [
            'cab_id' => $cab->id,
            'reservation_id' => $reservationId,
            'guest_name' => 'Dinesh Chugtai',
            'guest_phone' => '+1 415-888-9900',
            'pickup_location' => 'SFO Airport Terminal 2',
            'dropoff_location' => 'Pied Piper Luxury Resort',
            'pickup_time' => now()->addHours(3)->toDateTimeString(),
            'fare_amount' => 150.00,
            'status' => 'DISPATCHED',
        ]);
        $bookCabResp->assertStatus(302);

        // --------------------------------------------------------------------------------
        // 6. HRMS Full Lifecycle (Department, Employee, Shifts, Attendance, Leaves, Payroll)
        // --------------------------------------------------------------------------------
        $deptResp = $this->actingAs($user)->post('/departments', [
            'name' => 'Cloud Infrastructure & Guest Concierge',
            'description' => 'Oversees technical amenities and VIP guest service',
        ]);
        $deptResp->assertStatus(302);
        $dept = Department::where('name', 'Cloud Infrastructure & Guest Concierge')->first();

        $empResp = $this->actingAs($user)->post('/employees', [
            'name' => 'Bertram Gilfoyle',
            'email' => 'gilfoyle@piedpiper.com',
            'phone' => '+1 415-777-6655',
            'role' => 'ACCOUNTANT',
            'designation' => 'Systems Security Specialist',
            'department_id' => $dept->id,
            'salary' => 95000.00,
            'joining_date' => '2024-01-01',
        ]);
        $empResp->assertStatus(302);
        $employee = User::where('email', 'gilfoyle@piedpiper.com')->first();

        $shiftResp = $this->actingAs($user)->post('/shifts', [
            'name' => 'Night Cyber Operations Shift',
            'start_time' => '22:00:00',
            'end_time' => '06:00:00',
        ]);
        $shiftResp->assertStatus(302);
        $shift = Shift::where('name', 'Night Cyber Operations Shift')->first();

        $rosterResp = $this->actingAs($user)->post('/duty-roster', [
            'user_id' => $employee->id,
            'shift_id' => $shift->id,
            'duty_date' => now()->toDateString(),
            'status' => 'SCHEDULED',
        ]);
        $rosterResp->assertStatus(302);

        $attResp = $this->actingAs($user)->post('/attendance', [
            'user_id' => $employee->id,
            'date' => now()->toDateString(),
            'clock_in' => '22:00:00',
            'status' => 'PRESENT',
        ]);
        $attResp->assertStatus(302);

        $leaveResp = $this->actingAs($user)->post('/leaves', [
            'user_id' => $employee->id,
            'leave_type' => 'CASUAL',
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date' => now()->addDays(12)->toDateString(),
            'reason' => 'Server optimization & offline research',
        ]);
        $leaveResp->assertStatus(302);
        $leave = EmployeeLeave::where('user_id', $employee->id)->first();

        $approveLeaveResp = $this->actingAs($user)->post("/leaves/{$leave->id}/status", [
            'status' => 'APPROVED',
        ]);
        $approveLeaveResp->assertStatus(302);
        $this->assertEquals('APPROVED', $leave->fresh()->status);

        // Run automated payroll generation
        $payrollResp = $this->actingAs($user)->post('/payroll/generate', [
            'month' => (int) now()->format('m'),
            'year' => (int) now()->format('Y'),
        ]);
        $payrollResp->assertStatus(302);
        $payroll = Payroll::where('user_id', $employee->id)->first();
        $this->assertNotNull($payroll);

        // View generated payslip
        $viewPayslip = $this->actingAs($user)->get("/payroll/payslip/{$payroll->id}");
        $viewPayslip->assertStatus(200);
        $viewPayslip->assertSee('Bertram Gilfoyle');

        // --------------------------------------------------------------------------------
        // 7. Inventory Stock Lifecycle (Units, Categories, Items, Purchase, Damage, Reports)
        // --------------------------------------------------------------------------------
        $unitResp = $this->actingAs($user)->post('/inventory/units', [
            'name' => 'Pack of 100',
            'short_code' => 'pk100',
        ]);
        $unitResp->assertStatus(302);
        $unit = ItemUnit::where('short_code', 'pk100')->first();

        $catResp = $this->actingAs($user)->post('/inventory/categories', [
            'name' => 'Presidential Suite Consumables',
            'description' => 'Exclusive amenities and coffee beans',
        ]);
        $catResp->assertStatus(302);
        $category = ItemCategory::where('name', 'Presidential Suite Consumables')->first();

        $suppResp = $this->actingAs($user)->post('/inventory/suppliers', [
            'name' => 'Silicon Roasters Specialty Coffee',
            'company_name' => 'Silicon Roasters Inc',
            'email' => 'orders@siliconroasters.com',
            'phone' => '+1 415-333-2211',
        ]);
        $suppResp->assertStatus(302);
        $supplier = ItemSupplier::where('name', 'Silicon Roasters Specialty Coffee')->first();

        $itemResp = $this->actingAs($user)->post('/inventory/items', [
            'name' => 'Ethiopian Yirgacheffe Single Origin Beans 1kg',
            'sku' => 'COF-ETH-001',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'supplier_id' => $supplier->id,
            'purchase_price' => 30.00,
            'unit_price' => 45.00,
            'current_stock' => 50,
            'min_alert_stock' => 10,
        ]);
        $itemResp->assertStatus(302);
        $item = InventoryItem::where('sku', 'COF-ETH-001')->first();

        // Record Purchase In (Atomic Increment)
        $purchaseResp = $this->actingAs($user)->post('/inventory/purchase', [
            'item_id' => $item->id,
            'quantity' => 25,
            'unit_cost' => 30.00,
            'reference_no' => 'PO-COFFEE-8899',
            'notes' => 'Received from Silicon Roasters',
        ]);
        $purchaseResp->assertStatus(302);
        $this->assertEquals(75.00, (float) $item->fresh()->current_stock);

        // Record Damaged Write-off (Atomic Decrement)
        $damageResp = $this->actingAs($user)->post('/inventory/damage', [
            'item_id' => $item->id,
            'quantity' => 3,
            'reason' => 'Package tear during transit',
        ]);
        $damageResp->assertStatus(302);
        $this->assertEquals(72.00, (float) $item->fresh()->current_stock);

        // Download Stock & Purchase CSV Reports
        $stockCsvResp = $this->actingAs($user)->get('/stock-report.html?export=csv');
        $stockCsvResp->assertStatus(200);
        $this->assertStringContainsString('COF-ETH-001', $stockCsvResp->streamedContent());

        $purchaseCsvResp = $this->actingAs($user)->get('/purchase-report.html?export=csv');
        $purchaseCsvResp->assertStatus(200);
        $this->assertStringContainsString('PO-COFFEE-8899', $purchaseCsvResp->streamedContent());

        // --------------------------------------------------------------------------------
        // 8. Role & Permission Management (RBAC)
        // --------------------------------------------------------------------------------
        $createRoleResp = $this->actingAs($user)->post('/role-add', [
            'name' => 'Lead Concierge Specialist',
            'description' => 'Full control over amenities, cabs, and room allocations',
        ]);
        $createRoleResp->assertStatus(302);

        $permMatrixResp = $this->actingAs($user)->post('/role-permission', [
            'role_name' => 'Lead Concierge Specialist',
            'permissions' => ['reservations.view', 'reservations.create', 'cabs.manage', 'amenities.manage'],
        ]);
        $permMatrixResp->assertStatus(302);

        $assignRoleResp = $this->actingAs($user)->post('/role-assign', [
            'user_id' => $user->id,
            'role' => 'SUPERADMIN',
        ]);
        $assignRoleResp->assertStatus(302);

        // --------------------------------------------------------------------------------
        // 9. Communication Hub (Chat, Inbox, Calendar)
        // --------------------------------------------------------------------------------
        $chatResp = $this->actingAs($user)->post('/chat/send', [
            'receiver_id' => $user->id,
            'message' => 'Presidential Suite 1001 VIP check-in completed seamlessly.',
        ]);
        $chatResp->assertStatus(302);
        $this->assertDatabaseHas('chat_messages', [
            'message' => 'Presidential Suite 1001 VIP check-in completed seamlessly.',
        ]);

        $inboxResp = $this->actingAs($user)->post('/inbox/send', [
            'to' => 'management@piedpiperhotel.com',
            'subject' => 'VIP Arrival & Tech Summit Notification',
            'body' => 'Dinesh Chugtai and executives have successfully checked in.',
        ]);
        $inboxResp->assertStatus(302);
        $this->assertDatabaseHas('internal_emails', [
            'subject' => 'VIP Arrival & Tech Summit Notification',
        ]);

        $calResp = $this->actingAs($user)->post('/calendar/events', [
            'title' => 'VIP Tech Keynote Dinner',
            'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_time' => now()->addDay()->addHours(3)->format('Y-m-d H:i:s'),
            'event_type' => 'EVENT',
            'description' => 'Banquet Hall Keynote presentation',
        ]);
        $calResp->assertStatus(302);
        $this->assertDatabaseHas('calendar_events', [
            'title' => 'VIP Tech Keynote Dinner',
        ]);

        // --------------------------------------------------------------------------------
        // 10. Financial Gateways, Booking Reports & System Search
        // --------------------------------------------------------------------------------
        $saveGatewayResp = $this->actingAs($user)->post('/payment-set', [
            'gateway' => 'STRIPE',
            'is_enabled' => '1',
            'environment' => 'live',
            'currency_code' => 'USD',
            'publishable_key' => 'pk_live_piedpiper_production_key',
            'secret_key' => 'sk_live_piedpiper_production_secret',
            'webhook_secret' => 'whsec_production_secret',
        ]);
        $saveGatewayResp->assertStatus(302);

        $bookingCsvResp = $this->actingAs($user)->get('/booking-report.html?export=csv');
        $bookingCsvResp->assertStatus(200);
        $this->assertStringContainsString($reservationCode, $bookingCsvResp->streamedContent());

        // Universal Search
        $searchResp = $this->actingAs($user)->get('/search.html?q=Chugtai');
        $searchResp->assertStatus(200);
        $searchResp->assertSee('Dinesh Chugtai');

        // Executive Dashboard View Two
        $dashTwoResp = $this->actingAs($user)->get('/index-two.html');
        $dashTwoResp->assertStatus(200);
        $dashTwoResp->assertSee('Hotel Activity Stream');
    }
}
