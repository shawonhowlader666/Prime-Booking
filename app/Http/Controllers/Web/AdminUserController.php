<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    private function getHotelId(): int
    {
        return auth()->user()?->hotel_id ?? Hotel::value('id') ?? 1;
    }

    public function index(Request $request): View
    {
        $hotelId = $this->getHotelId();
        $users = User::where('hotel_id', $hotelId)->latest()->get();

        $stats = [
            'total_users' => $users->count(),
            'active_users' => $users->where('is_active', true)->count(),
            'inactive_users' => $users->where('is_active', false)->count(),
            'super_admins' => $users->whereIn('role', ['SUPERADMIN', 'HOTEL_OWNER'])->count(),
            'front_desk' => $users->where('role', 'RECEPTIONIST')->count(),
            'housekeeping' => $users->where('role', 'HOUSEKEEPER')->count(),
        ];

        return view('pages.role-list', compact('users', 'stats'));
    }

    public function roleAdd(Request $request): View
    {
        return view('pages.role-add');
    }

    public function store(Request $request): RedirectResponse
    {
        $hotelId = $this->getHotelId();

        $validated = $request->validate([
            'name' => 'nullable|string|max:100',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|email|unique:users,email',
            'role' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6',
            'about' => 'nullable|string|max:1000',
            'is_active' => 'nullable',
        ]);

        $fullName = $validated['name'] ?? trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));
        if (empty($fullName)) {
            $fullName = 'Staff Member';
        }

        User::create([
            'hotel_id' => $hotelId,
            'name' => $fullName,
            'email' => $validated['email'],
            'role' => $validated['role'] ?? 'MANAGER',
            'phone' => $validated['phone'] ?? null,
            'about_me' => $validated['about'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => isset($validated['is_active']) ? (bool)$validated['is_active'] : true,
        ]);

        return redirect()->route('admin.role.list')->with('success', "Staff account for '{$fullName}' created successfully!");
    }

    public function rolePermission(): View
    {
        return view('pages.role-permission');
    }

    public function saveRolePermission(Request $request): RedirectResponse
    {
        return redirect()->back()->with('success', 'Role permissions updated successfully.');
    }

    public function roleCreateList(): View
    {
        $roles = [
            ['id' => 1, 'name' => 'Booking Agent', 'description' => 'Booking & Reservation Access'],
            ['id' => 2, 'name' => 'Moderator', 'description' => 'Guest & Services Moderation'],
            ['id' => 3, 'name' => 'Manager', 'description' => 'Full Hotel Operations & Roster Control'],
            ['id' => 4, 'name' => 'Administrator', 'description' => 'Global Enterprise Control'],
            ['id' => 5, 'name' => 'Customer Service', 'description' => 'Guest Service & Inquiries'],
            ['id' => 6, 'name' => 'Accountant', 'description' => 'Financial Folio & Audit Operations'],
            ['id' => 7, 'name' => 'Housekeeper', 'description' => 'Room Cleanliness & Housekeeping Checklists'],
        ];

        return view('pages.role-create-list', compact('roles'));
    }

    public function roleAssign(): View
    {
        $hotelId = $this->getHotelId();
        $users = User::where('hotel_id', $hotelId)->latest()->get();

        return view('pages.role-assign', compact('users'));
    }

    public function saveRoleAssign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role' => 'required|string',
        ]);

        $user = User::find($validated['user_id']);
        if ($user) {
            $user->update(['role' => $validated['role']]);
        }

        return redirect()->back()->with('success', 'User role assigned successfully.');
    }

    public function roleAccessList(): View
    {
        $hotelId = $this->getHotelId();
        $users = User::where('hotel_id', $hotelId)->latest()->get();

        return view('pages.role-access-list', compact('users'));
    }

    public function toggleStatus(int $id, Request $request): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        return redirect()->back()->with('success', "Status for '{$user->name}' updated successfully.");
    }

    public function destroy(int $id, Request $request): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', "Staff account '{$user->name}' removed successfully.");
    }
}
