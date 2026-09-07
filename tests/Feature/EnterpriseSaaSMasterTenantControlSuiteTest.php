<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnterpriseSaaSMasterTenantControlSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected Hotel $masterHotel;
    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->masterHotel = Hotel::create([
            'name' => 'Prime Cloud PMS Headquarters',
            'slug' => 'prime-cloud-hq',
            'code' => 'HQ-001',
            'email' => 'admin@primepms.com',
            'currency' => 'BDT',
            'sync_api_key' => 'hq_prime_key_123',
            'sync_webhook_secret' => 'hq_prime_secret_456',
            'is_active' => true,
            'subscription_plan' => 'ENTERPRISE',
            'subscription_status' => 'ACTIVE',
            'subscription_expires_at' => now()->addYear(),
            'monthly_fee' => 0.00,
            'is_subscription_locked' => false,
        ]);

        $this->superAdmin = User::create([
            'hotel_id' => $this->masterHotel->id,
            'name' => 'SaaS Master Admin',
            'email' => 'masteradmin@primepms.com',
            'password' => bcrypt('password123'),
            'role' => 'SUPERADMIN',
            'is_active' => true,
        ]);
    }

    public function test_superadmin_can_view_master_tenant_control_center_and_metrics()
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/saas-tenants');
        $response->assertStatus(200);
        $response->assertSee('Master Tenant Control Center');
        $response->assertSee('Prime Cloud PMS Headquarters');
        $response->assertSee('HQ-001');
    }

    public function test_superadmin_can_provision_new_hotel_tenant()
    {
        $payload = [
            'name' => 'Seagull Luxury Resort & Spa',
            'code' => 'SGL-CXB',
            'email' => 'contact@seagullhotel.com',
            'phone' => '+8801711000000',
            'city' => "Cox's Bazar",
            'country' => 'Bangladesh',
            'currency' => 'BDT',
            'subscription_plan' => 'STANDARD',
            'monthly_fee' => 1000.00,
            'is_trial' => true,
            'trial_days' => 14,
            'owner_name' => 'Rafiqul Islam',
            'owner_email' => 'rafiq@seagullhotel.com',
            'owner_password' => 'SeagullPass2026',
        ];

        $response = $this->actingAs($this->superAdmin)->post('/admin/saas-tenants', $payload);
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.tenants.index'));

        $this->assertDatabaseHas('hotels', [
            'name' => 'Seagull Luxury Resort & Spa',
            'code' => 'SGL-CXB',
            'subscription_status' => 'TRIAL',
            'monthly_fee' => 1000.00,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'rafiq@seagullhotel.com',
            'role' => 'HOTEL_OWNER',
        ]);

        $hotel = Hotel::where('code', 'SGL-CXB')->first();
        $this->assertDatabaseHas('room_types', ['hotel_id' => $hotel->id]);
        $this->assertDatabaseHas('tenant_subscriptions', ['hotel_id' => $hotel->id]);
    }

    public function test_superadmin_can_update_tenant_details_and_plan()
    {
        $hotel = Hotel::create([
            'name' => 'Old Town Boutique Inn',
            'slug' => 'old-town-inn',
            'code' => 'OT-INN',
            'subscription_plan' => 'STANDARD',
            'subscription_status' => 'ACTIVE',
            'monthly_fee' => 1000.00,
        ]);

        $response = $this->actingAs($this->superAdmin)->put("/admin/saas-tenants/{$hotel->id}", [
            'name' => 'Old Town Luxury Heritage Boutique',
            'email' => 'manager@oldtownheritage.com',
            'subscription_plan' => 'PREMIUM',
            'subscription_status' => 'ACTIVE',
            'monthly_fee' => 2500.00,
            'subscription_expires_at' => now()->addMonths(6)->toDateString(),
        ]);

        $response->assertStatus(302);
        $hotel->refresh();

        $this->assertEquals('Old Town Luxury Heritage Boutique', $hotel->name);
        $this->assertEquals('PREMIUM', $hotel->subscription_plan);
        $this->assertEquals(2500.00, (float) $hotel->monthly_fee);
    }

    public function test_superadmin_can_suspend_activate_and_extend_tenant()
    {
        $hotel = Hotel::create([
            'name' => 'Sunset Beach Hotel',
            'slug' => 'sunset-beach',
            'code' => 'SSB-001',
            'subscription_plan' => 'STANDARD',
            'subscription_status' => 'ACTIVE',
            'monthly_fee' => 1000.00,
            'is_subscription_locked' => false,
        ]);

        // 1. Suspend
        $suspendResp = $this->actingAs($this->superAdmin)->post("/admin/saas-tenants/{$hotel->id}/toggle", [
            'action' => 'SUSPEND',
        ]);
        $suspendResp->assertStatus(302);
        $hotel->refresh();
        $this->assertEquals('SUSPENDED', $hotel->subscription_status);
        $this->assertTrue($hotel->is_subscription_locked);

        // 2. Activate
        $activateResp = $this->actingAs($this->superAdmin)->post("/admin/saas-tenants/{$hotel->id}/toggle", [
            'action' => 'ACTIVATE',
        ]);
        $activateResp->assertStatus(302);
        $hotel->refresh();
        $this->assertEquals('ACTIVE', $hotel->subscription_status);
        $this->assertFalse($hotel->is_subscription_locked);

        // 3. Extend +30 days
        $extendResp = $this->actingAs($this->superAdmin)->post("/admin/saas-tenants/{$hotel->id}/extend", [
            'days' => 30,
        ]);
        $extendResp->assertStatus(302);
        $hotel->refresh();
        $this->assertTrue($hotel->subscription_expires_at->isFuture());
    }

    public function test_superadmin_can_impersonate_and_switch_tenant_context()
    {
        $hotel = Hotel::create([
            'name' => 'Peninsula Grand Hotel',
            'slug' => 'peninsula-grand',
            'code' => 'PNS-001',
        ]);

        $response = $this->actingAs($this->superAdmin)->get("/admin/saas-tenants/{$hotel->id}/impersonate");
        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard'));

        $this->assertEquals($hotel->id, session('active_hotel_id'));
    }

    public function test_superadmin_can_safely_delete_hotel_tenant()
    {
        $hotel = Hotel::create([
            'name' => 'Temporary Motel',
            'slug' => 'temp-motel',
            'code' => 'TMP-001',
        ]);

        $response = $this->actingAs($this->superAdmin)->delete("/admin/saas-tenants/{$hotel->id}");
        $response->assertStatus(302);

        $this->assertDatabaseMissing('hotels', ['id' => $hotel->id]);
    }

    public function test_prime_booking_remote_provision_and_delete_api()
    {
        $masterSecret = config('app.key');

        // Remote Provision
        $provisionPayload = json_encode([
            'name' => 'Prime SkyLine Suites',
            'code' => 'PSS-777',
            'email' => 'skyline@primeaviation.com',
            'subscription_plan' => 'ENTERPRISE',
            'monthly_fee' => 5000.00,
            'is_trial' => false,
            'owner_email' => 'owner@primeskyline.com',
            'owner_password' => 'SkyLine2026Pass',
            'prime_booking_property_id' => 'PRIME-OTA-8888',
        ]);

        $signature = hash_hmac('sha256', $provisionPayload, $masterSecret);

        $response = $this->withHeaders([
            'X-Prime-Signature' => $signature,
            'Content-Type' => 'application/json',
        ])->postJson('/api/v1/sync/prime-booking/provision-tenant', json_decode($provisionPayload, true));

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('hotel.code', 'PSS-777');

        $this->assertDatabaseHas('hotels', ['code' => 'PSS-777']);

        // Remote Delete
        $hotel = Hotel::where('code', 'PSS-777')->first();
        $delSecret = $hotel->sync_webhook_secret;
        $delSig = hash_hmac('sha256', 'PSS-777', $delSecret);

        $delResponse = $this->withHeaders([
            'X-Prime-Signature' => $delSig,
        ])->deleteJson('/api/v1/sync/prime-booking/tenants/PSS-777');

        $delResponse->assertStatus(200);
        $delResponse->assertJsonPath('success', true);
        $this->assertDatabaseMissing('hotels', ['code' => 'PSS-777']);
    }
}
