<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Property;
use App\Models\Room;

/**
 * CoxsBazarHotelsTest
 *
 * Senior Engineering Practice:
 * Automated smoke and regression testing for newly imported inventory.
 */
class CoxsBazarHotelsTest extends TestCase
{
    public function test_exact_eleven_hotels_exist_in_database(): void
    {
        $count = Property::where('city', "Cox's Bazar")->count();
        $this->assertSame(11, $count, "Expected exactly 11 Cox's Bazar hotels in database.");
    }

    public function test_all_hotels_have_valid_rooms_and_cover_images(): void
    {
        $hotels = Property::where('city', "Cox's Bazar")->with('rooms')->get();

        foreach ($hotels as $hotel) {
            $this->assertNotEmpty($hotel->name, "Hotel name should not be empty");
            $this->assertNotEmpty($hotel->primary_image, "Hotel {$hotel->name} must have a primary image");
            $this->assertGreaterThan(0, $hotel->rooms->count(), "Hotel {$hotel->name} must have at least 1 room");
            $this->assertGreaterThan(0, $hotel->price_per_night, "Hotel {$hotel->name} must have a valid price");
            $this->assertNotEmpty($hotel->address, "Hotel {$hotel->name} must have an address");
            $this->assertNotEmpty($hotel->contact_phone, "Hotel {$hotel->name} must have a contact phone");
        }
    }

    public function test_hotel_detail_page_loads_successfully(): void
    {
        $hotel = Property::where('city', "Cox's Bazar")->first();
        $this->assertNotNull($hotel);

        $response = $this->get('/hotels/' . $hotel->id);
        $response->assertStatus(200);
        $response->assertSee($hotel->name);
    }

    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
