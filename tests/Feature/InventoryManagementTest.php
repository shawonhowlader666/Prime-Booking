<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\InventoryItem;
use App\Models\ItemCategory;
use App\Models\ItemSupplier;
use App\Models\ItemUnit;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Hotel $hotel;
    protected User $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Grand Palace Hotel',
            'code' => 'GPH',
            'slug' => 'grand-palace-hotel',
            'city' => 'Dhaka',
            'country' => 'Bangladesh',
            'is_active' => true,
        ]);

        $this->manager = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Inventory Manager',
            'email' => 'storekeeper@grandpalace.com',
            'password' => bcrypt('password123'),
            'role' => 'MANAGER',
            'is_active' => true,
        ]);
    }

    public function test_can_list_and_create_inventory_items(): void
    {
        $cat = ItemCategory::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Linens & Bedding',
            'is_active' => true,
        ]);

        $unit = ItemUnit::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Pieces',
            'short_code' => 'Pcs',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->manager)->post('/inventory/items', [
            'name' => 'King Duvet Cover White',
            'sku' => 'SKU-DUVET-001',
            'category_id' => $cat->id,
            'unit_id' => $unit->id,
            'purchase_price' => 1200.00,
            'current_stock' => 50,
            'min_alert_stock' => 10,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('inventory_items', [
            'hotel_id' => $this->hotel->id,
            'name' => 'King Duvet Cover White',
            'sku' => 'SKU-DUVET-001',
            'current_stock' => 50,
        ]);

        // Verify items page displays the registered item
        $listResponse = $this->actingAs($this->manager)->get('/items-list.html');
        $listResponse->assertStatus(200);
        $listResponse->assertSee('King Duvet Cover White');
        $listResponse->assertSee('SKU-DUVET-001');
    }

    public function test_can_manage_categories_and_units(): void
    {
        // Category
        $catResponse = $this->actingAs($this->manager)->post('/inventory/categories', [
            'name' => 'Room Toiletries',
            'description' => 'Guest bathroom essentials',
        ]);
        $catResponse->assertStatus(302);
        $this->assertDatabaseHas('item_categories', ['name' => 'Room Toiletries']);

        // Unit
        $unitResponse = $this->actingAs($this->manager)->post('/inventory/units', [
            'name' => 'Bottles',
            'short_code' => 'Btl',
        ]);
        $unitResponse->assertStatus(302);
        $this->assertDatabaseHas('item_units', ['short_code' => 'Btl']);
    }

    public function test_can_onboard_suppliers(): void
    {
        $response = $this->actingAs($this->manager)->post('/inventory/suppliers', [
            'name' => 'Tariqul Islam',
            'company_name' => 'Dhaka Hotel Supplies Ltd.',
            'phone' => '+880 1711-556677',
            'email' => 'tariqul@dhakahotelsupplies.com',
            'tax_number' => 'BIN-11223344',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('item_suppliers', [
            'company_name' => 'Dhaka Hotel Supplies Ltd.',
            'phone' => '+880 1711-556677',
        ]);
    }

    public function test_can_record_stock_purchase_with_atomic_increment(): void
    {
        $item = InventoryItem::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Plush Bath Mat',
            'sku' => 'SKU-MAT-01',
            'purchase_price' => 300.00,
            'current_stock' => 10,
            'min_alert_stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->manager)->post('/inventory/purchase', [
            'item_id' => $item->id,
            'quantity' => 20,
            'unit_cost' => 320.00,
            'reference_no' => 'PO-2026-9901',
            'notes' => 'Bulk restock from vendor',
        ]);

        $response->assertStatus(302);

        // Stock should now be 10 + 20 = 30
        $item->refresh();
        $this->assertEquals(30, (float) $item->current_stock);
        $this->assertEquals(320, (float) $item->purchase_price);

        $this->assertDatabaseHas('stock_movements', [
            'hotel_id' => $this->hotel->id,
            'item_id' => $item->id,
            'type' => 'PURCHASE_IN',
            'quantity' => 20,
            'total_cost' => 6400.00,
        ]);
    }

    public function test_can_record_damaged_stock_with_atomic_decrement(): void
    {
        $item = InventoryItem::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Glass Water Pitcher',
            'sku' => 'SKU-GLS-01',
            'purchase_price' => 400.00,
            'current_stock' => 15,
            'min_alert_stock' => 2,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->manager)->post('/inventory/damage', [
            'item_id' => $item->id,
            'quantity' => 3,
            'reason' => 'Broken during banquet service',
        ]);

        $response->assertStatus(302);

        // Stock should now be 15 - 3 = 12
        $item->refresh();
        $this->assertEquals(12, (float) $item->current_stock);

        $this->assertDatabaseHas('stock_movements', [
            'hotel_id' => $this->hotel->id,
            'item_id' => $item->id,
            'type' => 'DAMAGED',
            'quantity' => 3,
            'total_cost' => 1200.00,
        ]);
    }

    public function test_tenant_isolation_on_inventory(): void
    {
        $hotelB = Hotel::create([
            'name' => 'Seaside Resort Hotel',
            'code' => 'SRH',
            'slug' => 'seaside-resort-hotel',
            'city' => 'Coxs Bazar',
            'country' => 'Bangladesh',
            'is_active' => true,
        ]);

        InventoryItem::create([
            'hotel_id' => $hotelB->id,
            'name' => 'Seaside Beach Umbrella',
            'sku' => 'SKU-UMB-099',
            'purchase_price' => 1500.00,
            'current_stock' => 30,
            'is_active' => true,
        ]);

        // Manager of Grand Palace should NOT see Seaside Resort's items
        $response = $this->actingAs($this->manager)->get('/items-list.html');
        $response->assertStatus(200);
        $response->assertDontSee('Seaside Beach Umbrella');
    }

    public function test_stock_and_purchase_reports(): void
    {
        $item = InventoryItem::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Luxury Shampoo Bottle',
            'sku' => 'SKU-SHM-REP',
            'purchase_price' => 50.00,
            'current_stock' => 100,
            'min_alert_stock' => 20,
            'is_active' => true,
        ]);

        StockMovement::create([
            'hotel_id' => $this->hotel->id,
            'item_id' => $item->id,
            'user_id' => $this->manager->id,
            'type' => 'PURCHASE_IN',
            'quantity' => 100,
            'unit_cost' => 50.00,
            'total_cost' => 5000.00,
            'reference_no' => 'PO-REP-01',
        ]);

        $stockRepResponse = $this->actingAs($this->manager)->get('/stock-report.html');
        $stockRepResponse->assertStatus(200);
        $stockRepResponse->assertSee('Stock Report');

        $purchaseRepResponse = $this->actingAs($this->manager)->get('/purchase-report.html');
        $purchaseRepResponse->assertStatus(200);
        $purchaseRepResponse->assertSee('PO-REP-01');
        $purchaseRepResponse->assertSee('5,000.00');
    }
}
