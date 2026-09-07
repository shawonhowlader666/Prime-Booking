<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaaSSubscriptionAndPrimeBookingControlBridgeTest extends TestCase
{
    use RefreshDatabase;

    protected Hotel $hotel;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Grand Palace Luxury Suites',
            'slug' => 'grand-palace',
            'code' => 'GPLS-001',
            'email' => 'contact@grandpalace.com',
            'currency' => 'BDT',
            'sync_api_key' => 'prime_key_12345',
            'sync_webhook_secret' => 'prime_secret_67890',
            'is_active' => true,
            'subscription_plan' => 'STANDARD',
            'subscription_status' => 'ACTIVE',
            'subscription_expires_at' => now()->addDays(30),
            'monthly_fee' => 1000.00,
            'is_subscription_locked' => false,
        ]);

        $this->user = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Tanvir Ahmed',
            'email' => 'tanvir@grandpalace.com',
            'password' => bcrypt('password123'),
            'role' => 'HOTEL_OWNER',
            'is_active' => true,
        ]);
    }

    public function test_active_tenant_can_access_all_pms_pages_and_subscription_hub()
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);

        $subResponse = $this->actingAs($this->user)->get('/subscription');
        $subResponse->assertStatus(200);
        $subResponse->assertSee('Grand Palace Luxury Suites');
        $subResponse->assertSee('ACTIVE LICENSE');
    }

    public function test_expired_or_suspended_tenant_is_gated_by_middleware()
    {
        $this->hotel->update([
            'subscription_status' => 'EXPIRED',
            'subscription_expires_at' => now()->subDay(),
        ]);

        // Web request should redirect to /subscription
        $response = $this->actingAs($this->user)->get('/room-booking-list.html');
        $response->assertStatus(302);
        $response->assertRedirect(route('subscription.show'));

        // Subscription page should still be accessible
        $subPageResp = $this->actingAs($this->user)->get('/subscription');
        $subPageResp->assertStatus(200);
        $subPageResp->assertSee('EXPIRED / SUSPENDED');

        // API request should return 403 Forbidden
        $apiResp = $this->actingAs($this->user)->getJson('/api/v1/rooms');
        $apiResp->assertStatus(403);
        $apiResp->assertJsonPath('success', false);
    }

    public function test_tenant_can_renew_subscription_online()
    {
        $this->hotel->update([
            'subscription_status' => 'EXPIRED',
            'subscription_expires_at' => now()->subDay(),
            'is_subscription_locked' => true,
        ]);

        $renewResp = $this->actingAs($this->user)->post('/subscription/renew', [
            'months' => 3,
            'payment_method' => 'BKASH',
        ]);

        $renewResp->assertStatus(302);
        $renewResp->assertRedirect(route('subscription.show'));

        $this->hotel->refresh();
        $this->assertEquals('ACTIVE', $this->hotel->subscription_status);
        $this->assertFalse($this->hotel->is_subscription_locked);
        $this->assertTrue($this->hotel->subscription_expires_at->isFuture());

        $this->assertDatabaseHas('tenant_subscriptions', [
            'hotel_id' => $this->hotel->id,
            'amount' => 3000.00,
            'payment_method' => 'BKASH',
            'status' => 'PAID',
        ]);
    }

    public function test_prime_booking_remote_subscription_control_api()
    {
        $secret = $this->hotel->sync_webhook_secret;

        // 1. Prime Booking remotely SUSPENDS the hotel
        $suspendPayload = json_encode([
            'action' => 'SUSPEND',
            'notes' => 'Non-payment of monthly SaaS fee on Prime Booking portal',
        ]);
        $suspendSig = hash_hmac('sha256', $suspendPayload, $secret);

        $suspendResp = $this->withHeaders([
            'X-Hotel-Code' => $this->hotel->code,
            'X-Prime-Signature' => $suspendSig,
            'Content-Type' => 'application/json',
        ])->postJson('/api/v1/sync/prime-booking/subscription-control', json_decode($suspendPayload, true));

        $suspendResp->assertStatus(200);
        $suspendResp->assertJsonPath('hotel.subscription_status', 'SUSPENDED');
        $suspendResp->assertJsonPath('hotel.is_subscription_locked', true);

        $this->hotel->refresh();
        $this->assertFalse($this->hotel->isSubscriptionActive());

        // 2. Prime Booking remotely ACTIVATES and EXTENDS the hotel
        $extendPayload = json_encode([
            'action' => 'EXTEND',
            'plan' => 'ENTERPRISE',
            'monthly_fee' => 1200.00,
            'prime_booking_property_id' => 'PRIME-PROP-9999',
        ]);
        $extendSig = hash_hmac('sha256', $extendPayload, $secret);

        $extendResp = $this->withHeaders([
            'X-Hotel-Code' => $this->hotel->code,
            'X-Prime-Signature' => $extendSig,
            'Content-Type' => 'application/json',
        ])->postJson('/api/v1/sync/prime-booking/subscription-control', json_decode($extendPayload, true));

        $extendResp->assertStatus(200);
        $extendResp->assertJsonPath('hotel.subscription_status', 'ACTIVE');
        $extendResp->assertJsonPath('hotel.subscription_plan', 'ENTERPRISE');

        $this->hotel->refresh();
        $this->assertTrue($this->hotel->isSubscriptionActive());
        $this->assertEquals('ENTERPRISE', $this->hotel->subscription_plan);
        $this->assertEquals('PRIME-PROP-9999', $this->hotel->prime_booking_property_id);
    }

    public function test_prime_booking_telemetry_observation_stream()
    {
        $roomType = RoomType::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Deluxe King',
            'code' => 'DLX-K',
            'base_price' => 5000.00,
            'max_occupancy' => 2,
        ]);

        Room::create([
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $roomType->id,
            'room_number' => '101',
            'floor' => 1,
            'status' => 'AVAILABLE',
            'cleanliness' => 'CLEAN',
        ]);

        Room::create([
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $roomType->id,
            'room_number' => '102',
            'floor' => 1,
            'status' => 'OCCUPIED',
            'cleanliness' => 'CLEAN',
        ]);

        $telemetryResp = $this->withHeaders([
            'X-Hotel-Code' => $this->hotel->code,
        ])->getJson('/api/v1/sync/prime-booking/telemetry');

        $telemetryResp->assertStatus(200);
        $telemetryResp->assertJsonPath('success', true);
        $telemetryResp->assertJsonPath('hotel_code', $this->hotel->code);
        $telemetryResp->assertJsonPath('subscription.status', 'ACTIVE');
        $telemetryResp->assertJsonPath('inventory.total_rooms', 2);
        $telemetryResp->assertJsonPath('inventory.available_rooms', 1);
        $telemetryResp->assertJsonPath('inventory.occupied_rooms', 1);
        $this->assertEquals(50.0, (float) $telemetryResp->json('inventory.occupancy_percentage'));
    }
}
