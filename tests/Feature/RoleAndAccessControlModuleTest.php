<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAndAccessControlModuleTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotel;
    private User $admin;
    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Role Management Grand Resort',
            'slug' => 'role-management-grand-resort',
            'code' => 'RMGR-01',
        ]);

        $this->admin = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Chief Admin Officer',
            'email' => 'admin@grandresort.com',
            'role' => 'SUPERADMIN',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->staff = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Emily Receptionist',
            'email' => 'emily@grandresort.com',
            'role' => 'RECEPTIONIST',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    public function test_can_view_role_list_and_create_new_role_user(): void
    {
        $response = $this->actingAs($this->admin)->get('/role-list.html');
        $response->assertStatus(200);
        $response->assertSee('Role List');
        $response->assertSee('Emily Receptionist');

        $createResponse = $this->actingAs($this->admin)->post('/role-add', [
            'first_name' => 'David',
            'last_name' => 'Accountant',
            'email' => 'david@grandresort.com',
            'role' => 'ACCOUNTANT',
            'password' => 'SecurePass123!',
        ]);

        $createResponse->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'hotel_id' => $this->hotel->id,
            'name' => 'David Accountant',
            'email' => 'david@grandresort.com',
            'role' => 'ACCOUNTANT',
        ]);
    }

    public function test_can_view_and_save_role_permissions(): void
    {
        $response = $this->actingAs($this->admin)->get('/role-permission.html');
        $response->assertStatus(200);
        $response->assertSee('Role Permission');

        $saveResponse = $this->actingAs($this->admin)->post('/role-permission', [
            'role_name' => 'Duty Manager',
            'description' => 'Shift and roster permissions',
        ]);

        $saveResponse->assertStatus(302);
    }

    public function test_can_view_role_create_list(): void
    {
        $response = $this->actingAs($this->admin)->get('/role-create-list.html');
        $response->assertStatus(200);
        $response->assertSee('Role Create List');
        $response->assertSee('Administrator');
    }

    public function test_can_view_and_assign_roles(): void
    {
        $response = $this->actingAs($this->admin)->get('/role-assign.html');
        $response->assertStatus(200);
        $response->assertSee('Role Assign');
        $response->assertSee('Emily Receptionist');

        $assignResponse = $this->actingAs($this->admin)->post('/role-assign', [
            'user_id' => $this->staff->id,
            'role' => 'MANAGER',
        ]);

        $assignResponse->assertStatus(302);
        $this->assertEquals('MANAGER', $this->staff->fresh()->role);
    }

    public function test_can_view_role_access_list(): void
    {
        $response = $this->actingAs($this->admin)->get('/role-access-list.html');
        $response->assertStatus(200);
        $response->assertSee('Role Access List');
        $response->assertSee('Chief Admin Officer');
    }
}
