<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\VIPLoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VIPLoyaltyTest extends TestCase
{
    /** @test */
    public function vip_loyalty_service_returns_correct_bronze_tier_for_guest()
    {
        $service = app(VIPLoyaltyService::class);
        $result = $service->getUserTier(null);

        $this->assertEquals('Bronze', $result['tier']);
        $this->assertEquals('AgodaVIP Bronze', $result['tier_name_full']);
        $this->assertEquals(0.0, $result['discount_percent']);
        $this->assertEquals(2, $result['bookings_needed']);
    }

    /** @test */
    public function vip_public_page_is_accessible()
    {
        $response = $this->get('/vip');
        $response->assertStatus(200);
        $response->assertSee('What is AgodaVIP?');
        $response->assertSee('What are the benefits of AgodaVIP?');
    }

    /** @test */
    public function vip_public_api_returns_json_structure()
    {
        $response = $this->getJson('/api/v1/vip/tiers');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'tiers' => [
                'Bronze',
                'Silver',
                'Gold',
                'Platinum',
                'Diamond',
            ]
        ]);
    }
}
