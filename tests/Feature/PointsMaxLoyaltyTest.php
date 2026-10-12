<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class PointsMaxLoyaltyTest extends TestCase
{
    /** @test */
    public function guest_can_link_and_retrieve_pointsmax_program()
    {
        $response = $this->postJson(route('pointsmax.link'), [
            'program'       => 'Biman Loyalty Club',
            'membership_id' => 'BG-991203',
            'category'      => 'airline',
            'is_primary'    => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'program' => [
                'program'       => 'Biman Loyalty Club',
                'membership_id' => 'BG-991203',
                'is_primary'    => true,
            ]
        ]);

        // Check programs list endpoint
        $listResponse = $this->getJson(route('pointsmax.programs'));
        $listResponse->assertStatus(200);
        $listResponse->assertJsonStructure([
            'status',
            'programs' => [
                '*' => ['id', 'program', 'membership_id', 'is_primary']
            ]
        ]);
    }

    /** @test */
    public function authenticated_user_can_link_and_unlink_program()
    {
        $user = User::factory()->make([
            'id' => 99999,
            'name' => 'Test Flyer',
            'email' => 'flyer@test.com',
            'pointsmax_programs' => [],
        ]);

        $this->actingAs($user);

        $linkResponse = $this->postJson(route('pointsmax.link'), [
            'program'       => 'bKash Rewards',
            'membership_id' => '01712345678',
            'category'      => 'mfs',
            'is_primary'    => true,
        ]);

        $linkResponse->assertStatus(200);
        $programId = $linkResponse->json('program.id');
        $this->assertNotEmpty($programId);

        // Unlink
        $unlinkResponse = $this->deleteJson(route('pointsmax.unlink', $programId));
        $unlinkResponse->assertStatus(200);
        $unlinkResponse->assertJson([
            'success' => true,
        ]);
    }
}
