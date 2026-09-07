<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndAuthTest extends TestCase
{
    use RefreshDatabase;

    protected Hotel $hotel;
    protected User $adminUser;
    protected User $staffUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Grand Palace Hotel',
            'slug' => 'grand-palace',
            'code' => 'GPH-001',
            'currency' => 'BDT',
            'sync_api_key' => 'pms_test_api_key',
            'sync_webhook_secret' => 'super_secret_webhook_key_123',
            'is_active' => true,
        ]);

        $this->adminUser = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'General Manager',
            'email' => 'gm@grandpalace.com',
            'role' => 'HOTEL_OWNER',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $this->staffUser = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Housekeeper Alice',
            'email' => 'alice@grandpalace.com',
            'role' => 'HOUSEKEEPER',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_staff_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'gm@grandpalace.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($this->adminUser);
    }

    public function test_staff_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'gm@grandpalace.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_rbac_middleware_restricts_unauthorized_staff_from_admin_user_control(): void
    {
        // Housekeeper attempts to access admin user control
        $response = $this->actingAs($this->staffUser)->get('/admin/user-control');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_user_control(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/user-control');
        $response->assertStatus(200);
    }

    public function test_webhook_rejects_requests_with_missing_or_invalid_hmac_signature(): void
    {
        $payload = [
            'room_type_code' => 'DLX',
            'check_in_date' => now()->addDays(1)->toDateString(),
            'check_out_date' => now()->addDays(3)->toDateString(),
            'guest_first_name' => 'John',
            'total_amount' => 5000,
            'idempotency_key' => 'test-idem-1',
        ];

        // 1. Without Signature
        $response = $this->postJson('/api/v1/sync/prime-booking/webhook', $payload, [
            'X-Hotel-Code' => 'GPH-001',
        ]);
        $response->assertStatus(401);

        // 2. With Forged Signature
        $response = $this->postJson('/api/v1/sync/prime-booking/webhook', $payload, [
            'X-Hotel-Code' => 'GPH-001',
            'X-Prime-Signature' => 'fake_forged_signature_hash',
        ]);
        $response->assertStatus(401);
    }

    public function test_webhook_accepts_valid_hmac_signature(): void
    {
        RoomType::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Deluxe Suite',
            'code' => 'DLX',
            'base_price' => 5000,
            'max_adults' => 2,
            'max_children' => 1,
            'is_active' => true,
        ]);

        $payload = [
            'room_type_code' => 'DLX',
            'check_in_date' => now()->addDays(1)->toDateString(),
            'check_out_date' => now()->addDays(3)->toDateString(),
            'guest_first_name' => 'John',
            'guest_last_name' => 'Doe',
            'guest_email' => 'john@example.com',
            'total_amount' => 10000,
            'paid_amount' => 10000,
            'idempotency_key' => 'valid-ota-idem-1',
        ];

        $rawJson = json_encode($payload);
        $validSignature = hash_hmac('sha256', $rawJson, $this->hotel->sync_webhook_secret);

        $response = $this->call('POST', '/api/v1/sync/prime-booking/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_HOTEL_CODE' => 'GPH-001',
            'HTTP_X_PRIME_SIGNATURE' => $validSignature,
        ], $rawJson);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
