<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\DutyRoster;
use App\Models\EmployeeLeave;
use App\Models\Hotel;
use App\Models\Payroll;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class HrmsController extends Controller
{
    private function getActiveHotel(Request $request): Hotel
    {
        $user = $request->user();
        if ($user && $user->hotel_id) {
            return Hotel::find($user->hotel_id) ?? Hotel::first() ?? new Hotel(['name' => 'Grand Palace Hotel', 'id' => 1]);
        }
        return Hotel::first() ?? new Hotel(['name' => 'Grand Palace Hotel', 'id' => 1]);
    }

    // 1. Employee Directory
    public function employees(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $departments = Department::where('is_active', true)->get();

        $employees = User::with('department')
            ->where('hotel_id', $hotel->id)
            ->when($request->filled('department_id'), fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(fn($sub) => $sub->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"));
            })
            ->latest()
            ->paginate(12);

        return view('pages.employees-all', compact('hotel', 'employees', 'departments'));
    }

    public function createEmployee(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $departments = Department::where('is_active', true)->get();
        return view('pages.employees-add', compact('hotel', 'departments'));
    }

    public function storeEmployee(Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'role' => 'required|string|in:SUPERADMIN,MANAGER,RECEPTIONIST,HOUSEKEEPER,ACCOUNTANT',
            'department_id' => 'nullable|exists:departments,id',
            'designation' => 'nullable|string|max:100',
            'salary' => 'nullable|numeric|min:0',
            'joining_date' => 'nullable|date',
            'password' => 'nullable|string|min:6',
        ]);

        User::create([
            'hotel_id' => $hotel->id,
            'department_id' => $validated['department_id'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'designation' => $validated['designation'] ?? 'Staff',
            'salary' => $validated['salary'] ?? 0.00,
            'joining_date' => $validated['joining_date'] ?? now()->toDateString(),
            'password' => Hash::make($validated['password'] ?? 'password'),
            'is_active' => true,
        ]);

        return redirect()->route('hrms.employees')->with('success', 'Employee onboarded successfully!');
    }

    // 2. Departments Management
    public function departments(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $departments = Department::withCount('employees')->with('head')->latest()->get();
        $staff = User::where('hotel_id', $hotel->id)->get();

        return view('pages.employees-departments', compact('hotel', 'departments', 'staff'));
    }

    public function storeDepartment(Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'head_user_id' => 'nullable|exists:users,id',
        ]);

        Department::create([
            'hotel_id' => $hotel->id,
            'name' => $validated['name'],
            'code' => $validated['code'] ?? strtoupper(substr($validated['name'], 0, 3)),
            'description' => $validated['description'] ?? null,
            'head_user_id' => $validated['head_user_id'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('hrms.departments')->with('success', 'Department created successfully!');
    }

    // 3. Attendance Tracking
    public function attendance(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $date = $request->get('date', now()->toDateString());

        $attendances = Attendance::with('employee.department')
            ->where('hotel_id', $hotel->id)
            ->where('date', $date)
            ->get();

        $employees = User::where('hotel_id', $hotel->id)->where('is_active', true)->get();

        return view('pages.employees-attendance', compact('hotel', 'attendances', 'employees', 'date'));
    }

    public function recordAttendance(Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'status' => 'required|string|in:PRESENT,LATE,HALF_DAY,ABSENT,ON_LEAVE',
            'overtime_hours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        Attendance::updateOrCreate(
            ['hotel_id' => $hotel->id, 'user_id' => $validated['user_id'], 'date' => $validated['date']],
            [
                'clock_in' => $validated['clock_in'] ?? null,
                'clock_out' => $validated['clock_out'] ?? null,
                'status' => $validated['status'],
                'overtime_hours' => $validated['overtime_hours'] ?? 0.00,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Attendance record updated successfully!');
    }

    // 4. Shifts & Duty Rostering
    public function shifts(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $shifts = Shift::withCount('rosters')->latest()->get();

        return view('pages.duty-shift-list', compact('hotel', 'shifts'));
    }

    public function storeShift(Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        Shift::create([
            'hotel_id' => $hotel->id,
            'name' => $validated['name'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_active' => true,
        ]);

        return redirect()->route('hrms.shifts')->with('success', 'Work shift created successfully!');
    }

    public function roster(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $date = $request->get('date', now()->toDateString());

        $rosters = DutyRoster::with(['employee.department', 'shift'])
            ->where('hotel_id', $hotel->id)
            ->where('duty_date', $date)
            ->get();

        $employees = User::where('hotel_id', $hotel->id)->where('is_active', true)->get();
        $shifts = Shift::where('is_active', true)->get();

        return view('pages.duty-roster-list', compact('hotel', 'rosters', 'employees', 'shifts', 'date'));
    }

    public function assignRoster(Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'duty_date' => 'required|date',
            'status' => 'nullable|string|in:SCHEDULED,COMPLETED,SWAPPED,OFF',
            'notes' => 'nullable|string',
        ]);

        DutyRoster::updateOrCreate(
            ['hotel_id' => $hotel->id, 'user_id' => $validated['user_id'], 'duty_date' => $validated['duty_date']],
            [
                'shift_id' => $validated['shift_id'],
                'status' => $validated['status'] ?? 'SCHEDULED',
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Duty roster schedule assigned!');
    }

    // 5. Leave Management
    public function leaves(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);

        $leaves = EmployeeLeave::with(['employee.department', 'approvedBy'])
            ->where('hotel_id', $hotel->id)
            ->latest()
            ->paginate(15);

        $employees = User::where('hotel_id', $hotel->id)->where('is_active', true)->get();

        return view('pages.employees-leave', compact('hotel', 'leaves', 'employees'));
    }

    public function storeLeave(Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type' => 'required|string|in:CASUAL,SICK,ANNUAL,MATERNITY,UNPAID',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $d1 = \Carbon\Carbon::parse($validated['start_date']);
        $d2 = \Carbon\Carbon::parse($validated['end_date']);
        $totalDays = $d1->diffInDays($d2) + 1;

        EmployeeLeave::create([
            'hotel_id' => $hotel->id,
            'user_id' => $validated['user_id'],
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'] ?? null,
            'status' => 'PENDING',
        ]);

        return redirect()->route('hrms.leaves')->with('success', 'Leave application submitted successfully!');
    }

    public function updateLeaveStatus(int $id, Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);

        $leave = EmployeeLeave::where('hotel_id', $hotel->id)->where('id', $id)->firstOrFail();

        $validated = $request->validate([
            'status' => 'required|in:APPROVED,REJECTED',
        ]);

        $leave->status = $validated['status'];
        $leave->approved_by_user_id = $request->user()->id ?? null;
        $leave->save();

        return redirect()->back()->with('success', "Leave status updated to {$validated['status']}");
    }

    // 6. Payroll & Payslips
    public function payrolls(Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $month = $request->get('month', date('Y-m'));

        $payrolls = Payroll::with('employee.department')
            ->where('hotel_id', $hotel->id)
            ->where('month_year', $month)
            ->get();

        $employees = User::where('hotel_id', $hotel->id)->where('is_active', true)->get();

        return view('pages.payroll-employee-salary', compact('hotel', 'payrolls', 'employees', 'month'));
    }

    public function generatePayroll(Request $request): RedirectResponse
    {
        $hotel = $this->getActiveHotel($request);
        $month = $request->input('month_year', date('Y-m'));

        $employees = User::where('hotel_id', $hotel->id)->where('is_active', true)->get();

        foreach ($employees as $emp) {
            $base = $emp->salary > 0 ? $emp->salary : 25000.00;
            $allowances = $base * 0.10; // 10% standard hotel allowance
            $deductions = 0.00;
            $net = $base + $allowances - $deductions;

            Payroll::updateOrCreate(
                ['hotel_id' => $hotel->id, 'user_id' => $emp->id, 'month_year' => $month],
                [
                    'base_salary' => $base,
                    'allowances' => $allowances,
                    'overtime_amount' => 0.00,
                    'deductions' => $deductions,
                    'net_salary' => $net,
                    'status' => 'PAID',
                    'paid_at' => now(),
                    'payment_method' => 'BANK_TRANSFER',
                    'transaction_reference' => 'SAL-' . date('Ymd') . '-' . $emp->id,
                ]
            );
        }

        return redirect()->back()->with('success', "Payroll generated for all active staff for month: {$month}");
    }

    public function payslip(int $id, Request $request): View
    {
        $hotel = $this->getActiveHotel($request);
        $payroll = Payroll::with(['employee.department'])->where('hotel_id', $hotel->id)->where('id', $id)->firstOrFail();

        return view('pages.payroll-payslip', compact('hotel', 'payroll'));
    }
}
