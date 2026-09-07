<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Hotel;
use App\Models\Payroll;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrmsManagementTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotel;
    private User $manager;
    private User $receptionist;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Grand Palace Hotel',
            'code' => 'HOTEL-TEST-01',
            'slug' => 'grand-palace-hotel',
            'currency' => 'BDT',
            'is_active' => true,
        ]);

        $this->manager = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Operations Manager',
            'email' => 'manager@test.com',
            'password' => bcrypt('password'),
            'role' => 'MANAGER',
            'salary' => 60000,
            'is_active' => true,
        ]);

        $this->receptionist = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Front Desk Staff',
            'email' => 'reception@test.com',
            'password' => bcrypt('password'),
            'role' => 'RECEPTIONIST',
            'salary' => 30000,
            'is_active' => true,
        ]);
    }

    public function test_can_view_employee_directory(): void
    {
        $response = $this->actingAs($this->manager)->get('/employees');
        $response->assertStatus(200);
        $response->assertSee('Operations Manager');
        $response->assertSee('Front Desk Staff');
    }

    public function test_can_onboard_new_staff(): void
    {
        $response = $this->actingAs($this->manager)->post('/employees', [
            'name' => 'New Housekeeper',
            'email' => 'housekeeper.new@test.com',
            'phone' => '+880 1711-998877',
            'role' => 'HOUSEKEEPER',
            'designation' => 'Room Cleaner',
            'salary' => 20000,
            'joining_date' => now()->toDateString(),
            'password' => 'password123',
        ]);

        $response->assertRedirect('/employees');
        $this->assertDatabaseHas('users', [
            'email' => 'housekeeper.new@test.com',
            'role' => 'HOUSEKEEPER',
            'salary' => 20000,
        ]);
    }

    public function test_can_create_and_list_departments(): void
    {
        $response = $this->actingAs($this->manager)->post('/departments', [
            'name' => 'Housekeeping & Sanitation',
            'code' => 'HKD',
            'description' => 'Sanitation and hygiene department',
            'head_user_id' => $this->manager->id,
        ]);

        $response->assertRedirect('/departments');
        $this->assertDatabaseHas('departments', [
            'name' => 'Housekeeping & Sanitation',
            'code' => 'HKD',
        ]);

        $getResponse = $this->actingAs($this->manager)->get('/departments');
        $getResponse->assertStatus(200);
        $getResponse->assertSee('Housekeeping & Sanitation');
    }

    public function test_can_record_daily_attendance(): void
    {
        $response = $this->actingAs($this->manager)->post('/attendance', [
            'user_id' => $this->receptionist->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'status' => 'PRESENT',
            'overtime_hours' => 1.5,
            'notes' => 'On time arrival',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->receptionist->id,
            'status' => 'PRESENT',
            'overtime_hours' => 1.5,
        ]);
    }

    public function test_can_create_shifts_and_assign_duty_rosters(): void
    {
        $shift = Shift::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Morning Shift',
            'start_time' => '06:00:00',
            'end_time' => '14:00:00',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->manager)->post('/duty-roster', [
            'user_id' => $this->receptionist->id,
            'shift_id' => $shift->id,
            'duty_date' => now()->toDateString(),
            'notes' => 'Front desk lead',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('duty_rosters', [
            'user_id' => $this->receptionist->id,
            'shift_id' => $shift->id,
            'status' => 'SCHEDULED',
        ]);
    }

    public function test_leave_application_and_manager_approval(): void
    {
        // 1. Submit Leave
        $response = $this->actingAs($this->receptionist)->post('/leaves', [
            'user_id' => $this->receptionist->id,
            'leave_type' => 'CASUAL',
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'reason' => 'Family ceremony',
        ]);

        $response->assertRedirect('/leaves');
        $this->assertDatabaseHas('employee_leaves', [
            'user_id' => $this->receptionist->id,
            'leave_type' => 'CASUAL',
            'total_days' => 3,
            'status' => 'PENDING',
        ]);

        $leave = \App\Models\EmployeeLeave::first();

        // 2. Manager Approves Leave
        $approveResponse = $this->actingAs($this->manager)->post("/leaves/{$leave->id}/status", [
            'status' => 'APPROVED',
        ]);

        $approveResponse->assertStatus(302);
        $this->assertDatabaseHas('employee_leaves', [
            'id' => $leave->id,
            'status' => 'APPROVED',
            'approved_by_user_id' => $this->manager->id,
        ]);
    }

    public function test_payroll_generation_and_payslip_view(): void
    {
        $currentMonth = date('Y-m');

        $response = $this->actingAs($this->manager)->post('/payroll/generate', [
            'month_year' => $currentMonth,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('payrolls', [
            'user_id' => $this->receptionist->id,
            'month_year' => $currentMonth,
            'status' => 'PAID',
        ]);

        $payroll = Payroll::where('user_id', $this->receptionist->id)->first();
        $payslipResponse = $this->actingAs($this->manager)->get("/payroll/payslip/{$payroll->id}");
        $payslipResponse->assertStatus(200);
        $payslipResponse->assertSee('Payslip for the month');
        $payslipResponse->assertSee($this->receptionist->name);
    }
}
