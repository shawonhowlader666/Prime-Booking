<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\Property;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    // ─── List Users ───────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = User::with(['bookings:id,user_id,total_price', 'socialAccounts'])->withCount(['bookings','properties'])->latest();

        if ($role = $request->role and $role !== 'all') {
            $query->where('role', $role);
        }
        if ($status = $request->status and $status !== 'all') {
            $query->where('status', $status);
        }
        if ($search = $request->search) {
            $query->where(fn($q) => $q
                ->where('name',  'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
            );
        }

        $users = $query->paginate(25)->withQueryString();

        $stats = [
            'total'     => User::count(),
            'admins'    => User::whereIn('role', ['admin','super_admin'])->count(),
            'vendors'   => User::where('role', 'vendor')->count(),
            'customers' => User::where('role', 'customer')->count(),
            'banned'    => User::where('status', 'banned')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    // ─── User Detail ─────────────────────────────────────────────────────

    public function show($id)
    {
        $user     = User::withCount(['bookings','properties'])->findOrFail($id);
        $bookings = Booking::with('property:id,name,city,primary_image')
            ->where('user_id', $id)
            ->latest()
            ->paginate(10);
        $properties = Property::where('vendor_id', $id)->latest()->get();

        return view('admin.users.show', compact('user', 'bookings', 'properties'));
    }

    // ─── Create User ─────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:customer,vendor,admin',
            'phone'    => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'phone'    => $request->phone,
            'status'   => 'active',
        ]);

        $this->log('created', $user, "Created user account: {$user->email} (role: {$user->role})");

        return back()->with('success', "User \"{$user->name}\" created successfully.");
    }

    // ─── Ban User ─────────────────────────────────────────────────────────

    public function ban($id)
    {
        $user = User::findOrFail($id);

        if (in_array($user->role, ['super_admin'])) {
            return back()->with('error', 'Cannot ban a Super Admin account.');
        }

        $user->update(['status' => 'banned']);

        // Revoke all Sanctum API tokens immediately
        $user->tokens()->delete();

        // High-Security IP Firewall Ban
        $ip = request()->ip();
        if ($ip && !in_array($ip, ['127.0.0.1', '::1'])) {
            \App\Models\BannedIp::firstOrCreate(
                ['ip_address' => $ip],
                ['user_id' => $user->id, 'reason' => 'User Account Banned', 'banned_by' => auth()->user()?->name ?? 'Admin']
            );
        }

        $this->log('banned', $user, "BANNED user and blocked IP ({$ip}): {$user->name} ({$user->email})");

        return back()->with('success', "User \"{$user->name}\" and IP address have been banned from system.");
    }

    // ─── Unban / Activate ─────────────────────────────────────────────────

    public function activate($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);

        // Remove IP firewall ban if exists
        \App\Models\BannedIp::where('user_id', $user->id)->delete();

        $this->log('activated', $user, "Activated user: {$user->email}");
        return back()->with('success', "User \"{$user->name}\" is now active.");
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $role = $request->input('role', 'user');
        $user->update(['role' => $role]);
        $this->log('updated_role', $user, "Changed role of {$user->email} to {$role}");
        return back()->with('success', "User role updated to {$role} successfully.");
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        if ($user->status === 'banned') {
            return $this->activate($id);
        } else {
            return $this->ban($id);
        }
    }

    // ─── Promote to Vendor ────────────────────────────────────────────────

    public function promoteVendor($id)
    {
        $user = User::findOrFail($id);
        $old  = $user->role;
        $user->update(['role' => 'vendor']);
        $this->log('promoted', $user, "Promoted {$user->email} from {$old} → vendor");
        return back()->with('success', "\"{$user->name}\" is now a Vendor.");
    }

    // ─── Demote to Customer ───────────────────────────────────────────────

    public function demote($id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot demote an admin to customer from here. Use Settings.');
        }

        $user->update(['role' => 'customer']);
        $this->log('demoted', $user, "Demoted {$user->email} → customer");
        return back()->with('success', "\"{$user->name}\" is now a Customer.");
    }

    // ─── Update User ──────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:100',
            'email'  => 'required|email|unique:users,email,' . $id,
            'phone'  => 'nullable|string|max:20',
            'role'   => 'required|in:customer,vendor,admin',
            'status' => 'required|in:active,inactive,banned',
        ]);

        $user->update($request->only(['name','email','phone','role','status']));

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $this->log('updated', $user, "Updated user profile: {$user->email}");

        return back()->with('success', "User \"{$user->name}\" updated successfully.");
    }

    // ─── Delete User ─────────────────────────────────────────────────────

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot delete an admin account.');
        }

        $name = $user->name;
        $user->tokens()->delete();
        $user->delete();

        $this->log('deleted', null, "Deleted user: {$name} (ID: {$id})");

        return back()->with('success', "User \"{$name}\" deleted.");
    }

    // ─── Audit Helper ─────────────────────────────────────────────────────

    private function log(string $action, ?User $target, string $description): void
    {
        try {
            ActivityLog::create([
                'user_id'     => auth()->id(),
                'user_name'   => auth()->user()?->name ?? 'Admin',
                'action'      => $action,
                'model_type'  => 'User',
                'model_id'    => $target?->id,
                'description' => $description,
                'ip_address'  => request()->ip(),
            ]);
        } catch (\Exception $e) {}
    }
}
