<?php

namespace Tests\Feature;

use App\Models\AmenityService;
use App\Models\Department;
use App\Models\Hotel;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobileAndRestApiSuiteTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotel;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Grand Palace Hotel',
            'code' => 'HOTEL-API-01',
            'slug' => 'grand-palace-api',
            'currency' => 'BDT',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Mobile App User',
            'email' => 'mobile@grandpalace.com',
            'password' => bcrypt('password'),
            'role' => 'MANAGER',
            'is_active' => true,
        ]);
    }

    public function test_mobile_app_can_fetch_hrms_employees_api(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/hrms/employees');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
            'pagination' => ['current_page', 'total'],
        ]);
    }

    public function test_mobile_app_can_clock_in_and_clock_out(): void
    {
        Sanctum::actingAs($this->user);

        // Clock In
        $inResponse = $this->postJson('/api/v1/hrms/attendance/clock-in', [
            'notes' => 'Mobile NFC clock-in',
        ]);
        $inResponse->assertStatus(200);
        $inResponse->assertJson(['success' => true]);

        // Clock Out
        $outResponse = $this->postJson('/api/v1/hrms/attendance/clock-out');
        $outResponse->assertStatus(200);
        $outResponse->assertJson(['success' => true]);
    }

    public function test_mobile_app_can_fetch_inventory_and_record_purchase(): void
    {
        Sanctum::actingAs($this->user);

        $item = InventoryItem::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Cotton Bath Towel',
            'purchase_price' => 450.00,
            'current_stock' => 50,
            'is_active' => true,
        ]);

        $purchaseResponse = $this->postJson('/api/v1/inventory/purchase', [
            'item_id' => $item->id,
            'quantity' => 20,
            'unit_cost' => 450.00,
            'reference_no' => 'PO-MOB-01',
        ]);

        $purchaseResponse->assertStatus(201);
        $this->assertEquals(70, $item->fresh()->current_stock);
    }

    public function test_mobile_app_can_fetch_amenities_and_book(): void
    {
        Sanctum::actingAs($this->user);

        $amenity = AmenityService::create([
            'hotel_id' => $this->hotel->id,
            'category' => 'SPA',
            'name' => 'Swedish Aromatherapy Spa',
            'duration_minutes' => 60,
            'price' => 2500.00,
            'is_active' => true,
        ]);

        $bookResponse = $this->postJson('/api/v1/amenities/book', [
            'service_id' => $amenity->id,
            'booked_for_date' => now()->addDay()->toDateString(),
            'time_slot' => '10:00 - 11:00',
            'quantity' => 1,
        ]);

        $bookResponse->assertStatus(201);
        $bookResponse->assertJson(['success' => true]);
    }
}
