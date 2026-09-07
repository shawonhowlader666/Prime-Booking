<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_login_page_returns_a_successful_response(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $hotel = Hotel::create([
            'name' => 'Grand Palace Hotel',
            'slug' => 'grand-palace',
            'code' => 'GPH-01',
        ]);

        $user = User::create([
            'hotel_id' => $hotel->id,
            'name' => 'General Manager',
            'email' => 'gm@grandpalace.com',
            'role' => 'HOTEL_OWNER',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_my_profile(): void
    {
        $hotel = Hotel::create([
            'name' => 'Grand Palace Hotel',
            'slug' => 'grand-palace-2',
            'code' => 'GPH-02',
        ]);

        $user = User::create([
            'hotel_id' => $hotel->id,
            'name' => 'General Manager',
            'email' => 'gm2@grandpalace.com',
            'role' => 'HOTEL_OWNER',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/my-profile.html');
        $response->assertStatus(200);
        $response->assertSee('My Profile');
        $response->assertSee('Edit Profile');
        $response->assertSee('General Manager');
    }

    public function test_authenticated_user_can_update_profile(): void
    {
        $hotel = Hotel::create([
            'name' => 'Grand Palace Hotel & Resort',
            'slug' => 'grand-palace-profile-test',
            'code' => 'GPH-PROF-01',
            'city' => 'Dhaka',
            'country' => 'Bangladesh',
        ]);

        $user = User::create([
            'hotel_id' => $hotel->id,
            'name' => 'Tanvir Ahmed (Owner)',
            'email' => 'admin@primepms.com',
            'role' => 'HOTEL_OWNER',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/my-profile.html', [
            'name' => 'Tanvir Ahmed Updated',
            'email' => 'admin@primepms.com',
            'company' => 'Grand Palace Coxs Bazar Luxury Resort',
            'designation' => 'Executive Director',
            'address' => 'Plot 14, Marine Drive',
            'city' => 'Coxs Bazar',
            'country' => 'Bangladesh',
            'phone' => '+8801700000000',
            'about_me' => 'Leading hotelier and tech entrepreneur in Bangladesh.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertEquals('Tanvir Ahmed Updated', $user->fresh()->name);
        $this->assertEquals('Leading hotelier and tech entrepreneur in Bangladesh.', $user->fresh()->about_me);
        $this->assertEquals('Grand Palace Coxs Bazar Luxury Resort', $hotel->fresh()->name);
        $this->assertEquals('Coxs Bazar', $hotel->fresh()->city);
        $this->assertEquals('Executive Director', $user->fresh()->designation);
    }
}
