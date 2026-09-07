<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\TenantSubscription;
use App\Models\User;
use App\Services\DynamicPricingService;
use App\Services\GuestNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class EnterpriseApexEnhancementsSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected Hotel $hotel;
    protected User $admin;
    protected RoomType $roomType;
    protected Room $room;
    protected Guest $guest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Royal Palace Luxury Suites',
            'slug' => 'royal-palace-luxury-suites',
            'code' => 'ROYAL-001',
            'email' => 'contact@royalpalace.com',
            'phone' => '+1 800-555-0199',
            'address' => '100 Ocean Drive',
            'is_active' => true,
            'sync_webhook_secret' => 'apex_secret_key_123',
        ]);

        TenantSubscription::create([
            'hotel_id' => $this->hotel->id,
            'plan_name' => 'Enterprise Apex',
            'price' => 1000.00,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'trial_ends_at' => now()->addDays(30),
            'subscription_ends_at' => now()->addYear(),
            'max_rooms' => 500,
            'features' => ['all_features'],
        ]);

        $this->admin = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Alexander Apex',
            'email' => 'alexander.apex@royalpalace.com',
            'role' => 'SUPERADMIN',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $this->roomType = RoomType::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Executive Ocean View',
            'code' => 'EOV-01',
            'base_price' => 250.00,
            'max_occupancy' => 3,
        ]);

        $this->room = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $this->roomType->id,
            'room_number' => '101',
            'status' => 'AVAILABLE',
            'is_active' => true,
        ]);

        $this->guest = Guest::create([
            'hotel_id' => $this->hotel->id,
            'first_name' => 'Alexander',
            'last_name' => 'Hamilton',
            'email' => 'alexander@example.com',
            'phone' => '+15551234567',
        ]);
    }

    public function test_guest_notification_service_dispatches_messages_and_records_audit_trail(): void
    {
        $reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'guest_id' => $this->guest->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'reservation_code' => 'RES-NOTIF-99',
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(3)->toDateString(),
            'status' => 'CONFIRMED',
            'total_amount' => 750.00,
            'paid_amount' => 750.00,
            'source' => 'DIRECT',
        ]);

        $service = app(GuestNotificationService::class);

        // 1. Booking Confirmation
        $confirmation = $service->sendBookingConfirmation($reservation, 'whatsapp');
        $this->assertEquals('sent', $confirmation['status']);
        $this->assertEquals('whatsapp', $confirmation['channel']);
        $this->assertEquals('BOOKING_CONFIRMATION', $confirmation['event_type']);
        $this->assertStringContainsString('Alexander Hamilton', $confirmation['content']);

        // 2. Check-in Welcome
        $welcome = $service->sendCheckInWelcome($reservation, 'sms');
        $this->assertEquals('sent', $welcome['status']);
        $this->assertEquals('CHECK_IN_WELCOME', $welcome['event_type']);
        $this->assertStringContainsString('101', $welcome['content']);

        // 3. Check-out Thank you
        $thankYou = $service->sendCheckOutThankYou($reservation, 'whatsapp');
        $this->assertEquals('sent', $thankYou['status']);
        $this->assertEquals('CHECK_OUT_SUMMARY', $thankYou['event_type']);

        // Assert Audit Logs created
        $this->assertDatabaseHas('audit_logs', [
            'hotel_id' => $this->hotel->id,
            'action' => 'NOTIFICATION_DISPATCHED',
            'entity_type' => Reservation::class,
            'entity_id' => $reservation->id,
        ]);
    }

    public function test_forensic_activity_audit_middleware_records_mutations_and_sanitizes_secrets(): void
    {
        $this->actingAs($this->admin);

        // Perform a mutating action with sensitive parameters
        $response = $this->postJson('/api/v1/reservations', [
            'guest_first_name' => 'Eleanor',
            'guest_last_name' => 'Vance',
            'guest_email' => 'eleanor@vance.com',
            'guest_phone' => '+15559876543',
            'room_type_id' => $this->roomType->id,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'adults' => 2,
            'total_amount' => 500.00,
            'password' => 'super_secret_pin',
            'credit_card_number' => '4111222233334444',
        ]);

        $response->assertStatus(201);

        // Verify that the action was captured in audit_logs
        $auditLog = AuditLog::where('hotel_id', $this->hotel->id)
            ->where('entity_type', 'HTTP_REQUEST')
            ->latest('id')
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals($this->admin->id, $auditLog->user_id);
        $this->assertStringContainsString('POST', $auditLog->action);

        // Verify password and credit card number were purged/sanitized
        $payload = $auditLog->new_values['payload'] ?? [];
        $this->assertArrayNotHasKey('password', $payload);
        $this->assertArrayNotHasKey('credit_card_number', $payload);
        $this->assertEquals(2, $payload['adults']);
    }

    public function test_room_timeline_matrix_api_returns_accurate_30_day_availability_grid(): void
    {
        $this->actingAs($this->admin);

        // Create an active reservation
        Reservation::create([
            'hotel_id' => $this->hotel->id,
            'guest_id' => $this->guest->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'reservation_code' => 'RES-TIMELINE-1',
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'status' => 'CONFIRMED',
            'total_amount' => 500.00,
            'source' => 'DIRECT',
        ]);

        $response = $this->getJson('/api/v1/rooms/timeline-matrix?days=30');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'start_date',
                'end_date',
                'total_days',
                'total_rooms',
                'dates',
                'daily_summary',
                'matrix',
            ]);

        $data = $response->json();
        $this->assertEquals(30, $data['total_days']);
        $this->assertEquals(1, $data['total_rooms']);
        $this->assertEquals('101', $data['matrix'][0]['room_number']);

        $todayStr = now()->format('Y-m-d');
        $this->assertEquals('RESERVED', $data['matrix'][0]['schedule'][$todayStr]['status']);
    }

    public function test_automated_night_audit_command_executes_flawlessly(): void
    {
        $exitCode = Artisan::call('pms:night-audit', ['--hotel' => $this->hotel->code]);

        $this->assertEquals(0, $exitCode);
    }

    public function test_dynamic_pricing_service_calculates_rates_with_occupancy_surges(): void
    {
        $service = app(DynamicPricingService::class);
        $result = $service->calculateDynamicRate($this->roomType, now()->toDateString(), 3);

        $this->assertEquals($this->roomType->id, $result['room_type_id']);
        $this->assertEquals(250.00, $result['base_price']);
        $this->assertEquals(3, $result['nights']);
        $this->assertIsArray($result['daily_breakdown']);
        $this->assertGreaterThan(0, $result['recommended_total_price']);
    }

    public function test_pos_direct_room_charge_billing_posts_to_guest_folio(): void
    {
        $this->actingAs($this->admin);

        $reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'guest_id' => $this->guest->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'reservation_code' => 'RES-POS-001',
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'status' => 'CHECKED_IN',
            'total_amount' => 500.00,
            'paid_amount' => 500.00,
            'source' => 'DIRECT',
        ]);

        $response = $this->postJson('/api/v1/pos/charge-room', [
            'room_number' => '101',
            'outlet' => 'Sky Lounge Bar',
            'description' => 'Fine Dining & Drinks',
            'amount' => 120.00,
            'tax_amount' => 18.00,
            'service_charge' => 12.00,
            'pos_reference' => 'POS-INV-888',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('folio_items', [
            'hotel_id' => $this->hotel->id,
            'item_type' => 'POS_OUTLET_CHARGE',
            'total_amount' => 150.00,
        ]);
    }

    public function test_check_tenant_subscriptions_command_marks_expired_accounts(): void
    {
        // Create an expired subscription
        $expiredHotel = Hotel::create([
            'name' => 'Expired Inn',
            'slug' => 'expired-inn',
            'code' => 'EXP-999',
            'email' => 'expired@example.com',
            'phone' => '+15559990000',
            'address' => '99 Sunset Boulevard',
            'is_active' => true,
        ]);

        $sub = TenantSubscription::create([
            'hotel_id' => $expiredHotel->id,
            'plan_name' => 'Basic Plan',
            'amount' => 1000.00,
            'currency' => 'BDT',
            'payment_method' => 'BKASH',
            'transaction_reference' => 'TXN-EXP-001',
            'status' => 'active',
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->subDays(5),
        ]);

        $exitCode = Artisan::call('pms:check-subscriptions');
        $this->assertEquals(0, $exitCode);

        $this->assertEquals('expired', $sub->fresh()->status);
    }
}
