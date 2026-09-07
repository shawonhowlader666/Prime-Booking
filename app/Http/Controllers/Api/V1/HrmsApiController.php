<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\DutyRoster;
use App\Models\EmployeeLeave;
use App\Models\Payroll;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HrmsApiController extends Controller
{
    private function getHotelId(Request $request): int
    {
        return $request->user()?->hotel_id ?? 1;
    }

    public function employees(Request $request): JsonResponse
    {
        $hotelId = $this->getHotelId($request);

        $employees = User::with('department')
            ->where('hotel_id', $hotelId)
            ->when($request->filled('department_id'), fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(fn($sub) => $sub->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
            })
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Employees fetched successfully',
            'data' => $employees->items(),
            'pagination' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'total' => $employees->total(),
                'per_page' => $employees->perPage(),
            ],
        ]);
    }

    public function departments(Request $request): JsonResponse
    {
        $hotelId = $this->getHotelId($request);
        $departments = Department::withCount('employees')->where('hotel_id', $hotelId)->get();

        return response()->json([
            'success' => true,
            'message' => 'Departments fetched successfully',
            'data' => $departments,
        ]);
    }

    public function clockIn(Request $request): JsonResponse
    {
        $hotelId = $this->getHotelId($request);
        $userId = $request->user()?->id ?? $request->input('user_id');
        $date = $request->input('date', now()->toDateString());

        $attendance = Attendance::updateOrCreate(
            ['hotel_id' => $hotelId, 'user_id' => $userId, 'date' => $date],
            [
                'clock_in' => now()->format('H:i:s'),
                'status' => 'PRESENT',
                'notes' => $request->input('notes'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Clock-in recorded successfully',
            'data' => $attendance,
        ]);
    }

    public function clockOut(Request $request): JsonResponse
    {
        $hotelId = $this->getHotelId($request);
        $userId = $request->user()?->id ?? $request->input('user_id');
        $date = $request->input('date', now()->toDateString());

        $attendance = Attendance::where('hotel_id', $hotelId)
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->first();

        if (!$attendance) {
            $attendance = Attendance::create([
                'hotel_id' => $hotelId,
                'user_id' => $userId,
                'date' => $date,
                'clock_in' => now()->subHours(8)->format('H:i:s'),
                'clock_out' => now()->format('H:i:s'),
                'status' => 'PRESENT',
            ]);
        } else {
            $attendance->update([
                'clock_out' => now()->format('H:i:s'),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Clock-out recorded successfully',
            'data' => $attendance,
        ]);
    }

    public function leaves(Request $request): JsonResponse
    {
        $hotelId = $this->getHotelId($request);

        $leaves = EmployeeLeave::with(['employee.department', 'approvedBy'])
            ->where('hotel_id', $hotelId)
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $leaves->items(),
            'pagination' => [
                'current_page' => $leaves->currentPage(),
                'total' => $leaves->total(),
            ],
        ]);
    }

    public function applyLeave(Request $request): JsonResponse
    {
        $hotelId = $this->getHotelId($request);

        $validated = $request->validate([
            'leave_type' => 'required|string|in:CASUAL,SICK,ANNUAL,MATERNITY,UNPAID',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $d1 = \Carbon\Carbon::parse($validated['start_date']);
        $d2 = \Carbon\Carbon::parse($validated['end_date']);
        $totalDays = $d1->diffInDays($d2) + 1;

        $leave = EmployeeLeave::create([
            'hotel_id' => $hotelId,
            'user_id' => $request->user()->id,
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'] ?? null,
            'status' => 'PENDING',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leave application submitted successfully',
            'data' => $leave,
        ], 201);
    }
}
