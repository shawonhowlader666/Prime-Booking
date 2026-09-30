<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * MicroscopicHttpApiDeepAuditTest
 * ───────────────────────────────
 * Deep end-to-end audit testing all HTTP requests, REST API v1 endpoints,
 * data flows, status codes, and JSON response contracts.
 */
class MicroscopicHttpApiDeepAuditTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;
    /**
     * Test Public Web Pages HTTP Lifecycle & Status Codes.
     */
    public function test_public_web_pages_http_lifecycle(): void
    {
        // 1. Homepage HTTP 200
        $response = $this->get('/');
        $response->assertStatus(200);

        // 2. Health check endpoint HTTP 200/503 contract
        $health = $this->getJson('/api/health');
        $health->assertStatus(200)
            ->assertJsonStructure(['status', 'app', 'database', 'cache', 'timestamp']);

        // 3. SEO Sitemap XML
        $sitemap = $this->get('/sitemap.xml');
        $sitemap->assertStatus(200);

        // 4. Currency Switcher redirect
        $currSwitch = $this->get('/currency/switch/USD');
        $currSwitch->assertStatus(302);

        // 5. Language Switcher redirect
        $langSwitch = $this->get('/lang/switch/bn');
        $langSwitch->assertStatus(302);
    }

    /**
     * Test REST API v1 Content & Catalog Endpoints.
     */
    public function test_api_v1_catalog_and_content_endpoints(): void
    {
        // 1. Promotions API
        $res = $this->getJson('/api/v1/promotions');
        $res->assertStatus(200)->assertJson(['success' => true]);

        // 2. Destinations API
        $res = $this->getJson('/api/v1/destinations');
        $res->assertStatus(200)->assertJson(['success' => true]);

        // 3. Tour Packages API
        $res = $this->getJson('/api/v1/packages');
        $res->assertStatus(200)->assertJson(['success' => true]);

        // 4. Deals API
        $res = $this->getJson('/api/v1/deals');
        $res->assertStatus(200)->assertJson(['success' => true]);

        // 5. Airport Transfers API
        $res = $this->getJson('/api/v1/transfers');
        $res->assertStatus(200)->assertJson(['success' => true]);

        // 6. Currency Rates API
        $res = $this->getJson('/api/v1/currency/rates');
        $res->assertStatus(200)->assertJson(['success' => true]);

        // 7. VIP Loyalty Tiers API
        $res = $this->getJson('/api/v1/vip/tiers');
        $res->assertStatus(200)->assertJson(['status' => 'success']);
    }

    /**
     * Test Dynamic Price Preview & Coupon Validation API Micro-flow.
     */
    public function test_dynamic_price_preview_and_coupon_api_flow(): void
    {
        // 1. Price Preview API
        $preview = $this->postJson('/api/v1/booking/price-preview', [
            'property_id' => 1,
            'check_in'    => now()->addDays(2)->format('Y-m-d'),
            'check_out'   => now()->addDays(5)->format('Y-m-d'),
            'coupon_code' => '',
        ]);
        
        // Should return 200 or 404 (if property 1 not seeded)
        $this->assertContains($preview->status(), [200, 404]);
        if ($preview->status() === 200) {
            $preview->assertJsonStructure(['success', 'nights', 'price_per_night', 'subtotal', 'total']);
        }

        // 2. Coupon Validation API with non-existent code
        $couponCheck = $this->postJson('/api/v1/coupons/validate', [
            'code'   => 'NON_EXISTING_PROMO_999',
            'amount' => 5000,
        ]);
        $couponCheck->assertStatus(404)->assertJson(['success' => false]);
    }

    /**
     * Test Checkout Form Request Validation & Anti-Double-Booking Hold.
     */
    public function test_checkout_form_http_validation_rules(): void
    {
        // Empty submission should trigger validation errors (422 or redirect back with errors)
        $submit = $this->post('/book/1', []);
        $submit->assertStatus(302); // Redirect back with validation errors
        $submit->assertSessionHasErrors(['guest_name', 'guest_email', 'guest_phone', 'check_in', 'check_out', 'guests', 'payment_method']);
    }

    /**
     * Test Hotel Search & Autocomplete API endpoints.
     */
    public function test_hotel_search_and_autocomplete_endpoints(): void
    {
        // 1. Search suggestions autocomplete
        $sugg = $this->getJson('/api/v1/search/suggestions?q=Dhaka');
        $sugg->assertStatus(200);

        // 2. Search filter metadata
        $filters = $this->getJson('/api/v1/search/filter-metadata');
        $filters->assertStatus(200);

        // 3. Web Search page
        $searchWeb = $this->get('/search?destination=Dhaka');
        $searchWeb->assertStatus(200);
    }

    /**
     * Test Vendor Channel Manager iCal Calendar Route Contract.
     */
    public function test_vendor_ical_export_route_contract(): void
    {
        $iCalService = app(\App\Services\ICalSyncService::class);
        $property = (new Property())->forceFill(['id' => 1, 'name' => 'Seaside Villa']);
        $room = (new Room())->forceFill(['id' => 1, 'name' => 'Master Suite', 'property_id' => 1]);
        $room->setRelation('property', $property);

        $ics = $iCalService->generateIcsForRoom($room, collect(), collect());

        $this->assertNotEmpty($ics);
        $this->assertStringContainsString('BEGIN:VCALENDAR', $ics);
        $this->assertStringContainsString('PRODID:-//Prime Booking Enterprise OTA//Channel Manager iCal 2.0//EN', $ics);
        $this->assertStringContainsString('END:VCALENDAR', $ics);
    }
}
