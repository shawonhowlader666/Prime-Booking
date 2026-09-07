<?php

namespace Tests\Unit;

use App\Models\Hotel;
use App\Models\RatePlan;
use App\Models\RoomType;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_pricing_service_calculates_subtotal_tax_and_service_charge(): void
    {
        $hotel = Hotel::create([
            'name' => 'City Center Suites',
            'slug' => 'city-center-suites',
            'code' => 'CCS-01',
            'tax_percentage' => 15.00,
            'service_charge_percentage' => 5.00,
            'is_active' => true,
        ]);

        $roomType = RoomType::create([
            'hotel_id' => $hotel->id,
            'name' => 'Standard Single',
            'code' => 'STD-1',
            'base_price' => 1000.00,
        ]);

        $pricingService = new PricingService();

        // 2 nights: 2026-09-07 (Mon) to 2026-09-09 (Wed) -> 2 weeknights at base price 1000 = 2000
        $result = $pricingService->calculateStayPrice(
            $hotel,
            $roomType,
            '2026-09-07',
            '2026-09-09',
            1
        );

        $this->assertEquals(2, $result['nights']);
        $this->assertEquals(2000.00, $result['subtotal']);
        $this->assertEquals(100.00, $result['service_charge_amount']); // 5% of 2000
        $this->assertEquals(300.00, $result['tax_amount']); // 15% of 2000
        $this->assertEquals(2400.00, $result['grand_total']); // 2000 + 100 + 300
    }
}
