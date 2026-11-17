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

        $users = $query->paginate(per_page(25))->withQueryString();

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
            ->paginate(per_page(10));
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
            'role'     => 'required|in:customer,vendor,admin,super_admin',
            'phone'    => 'nullable|string|max:20',
        ]);

        $role = $request->role;
        if (in_array($role, ['admin', 'super_admin']) && !auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Only Super Admin can create administrative accounts.');
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $role,
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

        if ($user->role === 'super_admin' || ($user->role === 'admin' && !auth()->user()->isSuperAdmin())) {
            return back()->with('error', 'Unauthorized to suspend administrative accounts.');
        }

        $user->update(['status' => 'banned']);

        // Revoke all Sanctum API tokens immediately
        $user->tokens()->delete();

        // High-Security IP Firewall Ban: ban the target user's last recorded IP from activity logs, never the admin's own IP
        $adminIp = request()->ip();
        $targetIp = \App\Models\ActivityLog::where('user_id', $user->id)
            ->whereNotNull('ip_address')
            ->latest()
            ->value('ip_address');

        if ($targetIp && !in_array($targetIp, ['127.0.0.1', '::1', $adminIp])) {
            \App\Models\BannedIp::firstOrCreate(
                ['ip_address' => $targetIp],
                ['user_id' => $user->id, 'reason' => 'User Account Banned', 'banned_by' => auth()->user()?->name ?? 'Admin']
            );
        }

        $this->log('banned', $user, "BANNED user account: {$user->name} ({$user->email})" . ($targetIp ? " [IP: {$targetIp}]" : ""));

        return back()->with('success', "User \"{$user->name}\" has been banned successfully.");
    }

    // ─── Unban / Activate ─────────────────────────────────────────────────

    public function activate($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'super_admin' || ($user->role === 'admin' && !auth()->user()->isSuperAdmin())) {
            return back()->with('error', 'Unauthorized to modify administrative accounts.');
        }

        $user->update(['status' => 'active']);

        // Remove IP firewall ban if exists
        \App\Models\BannedIp::where('user_id', $user->id)->delete();

        $this->log('activated', $user, "Activated user: {$user->email}");
        return back()->with('success', "User \"{$user->name}\" is now active.");
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:customer,vendor,admin,super_admin',
        ]);
        $targetRole = $request->input('role');
        $user = User::findOrFail($id);

        // Modifying or assigning admin/super_admin roles requires super_admin
        if (($targetRole === 'admin' || $targetRole === 'super_admin' || $user->isAdmin()) && !auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Only Super Admin can assign or alter administrative roles.');
        }

        // Prevent self-demotion if caller is super_admin
        if ($user->id === auth()->id() && $user->role === 'super_admin' && $targetRole !== 'super_admin') {
            return back()->with('error', 'Cannot demote the active Super Admin account.');
        }

        $user->update(['role' => $targetRole]);
        $this->log('updated_role', $user, "Changed role of {$user->email} to {$targetRole}");
        return back()->with('success', "User role updated to {$targetRole} successfully.");
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
        if ($user->isAdmin() && !auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Only Super Admin can alter administrative accounts.');
        }
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

        // Security check: Only Super Admin can edit Super Admin or other Admin profiles
        if (($user->isSuperAdmin() || $user->role === 'admin') && !auth()->user()->isSuperAdmin() && auth()->id() !== $user->id) {
            return back()->with('error', 'Only Super Admin can update administrative profiles.');
        }

        $request->validate([
            'name'   => 'required|string|max:100',
            'email'  => 'required|email|unique:users,email,' . $id,
            'phone'  => 'nullable|string|max:20',
            'role'   => 'required|in:customer,vendor,admin,super_admin',
            'status' => 'required|in:active,inactive,banned',
        ]);

        $roleToSet = $request->role;
        // Non-super-admins cannot elevate anyone to admin/super_admin
        if (in_array($roleToSet, ['admin', 'super_admin']) && !auth()->user()->isSuperAdmin()) {
            $roleToSet = $user->role;
        }

        $updateData = $request->only(['name','email','phone','status']);
        $updateData['role'] = $roleToSet;

        $user->update($updateData);

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

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own active administrator account.');
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot delete Super Admin account.');
        }

        if ($user->isAdmin() && !auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Only Super Admin can delete admin accounts.');
        }

        // Security Guard: Prevent deleting vendors with active or upcoming reservations
        if ($user->isVendor()) {
            $hasActiveBookings = Booking::whereHas('property', fn($q) => $q->where('vendor_id', $user->id))
                ->whereIn('status', ['confirmed', 'pending'])
                ->where('check_out', '>=', now()->toDateString())
                ->exists();
            if ($hasActiveBookings) {
                return back()->with('error', "Cannot delete vendor \"{$user->name}\" because their properties have active or upcoming guest reservations.");
            }
        }

        $name = $user->name;
        $user->tokens()->delete();
        $user->delete();

        $this->log('deleted', null, "Deleted user: {$name} (ID: {$id})");

        return back()->with('success', "User \"{$name}\" deleted.");
    }

    // ─── Bulk Actions ─────────────────────────────────────────────────────

    public function bulkAction(Request $request)
    {
        $action  = $request->input('action');
        $userIds = $request->input('user_ids', []);

        if (empty($userIds) || !is_array($userIds)) {
            return back()->with('error', 'Please select at least one user to perform bulk action.');
        }

        // Exclude current logged in admin and super_admins from destructive actions
        $targetUsers = User::whereIn('id', $userIds)
            ->where('id', '!=', auth()->id())
            ->where('role', '!=', 'super_admin')
            ->get();

        $count = $targetUsers->count();

        if ($count === 0) {
            return back()->with('error', 'No eligible users found for bulk action (super admins and your own account are protected).');
        }

        switch ($action) {
            case 'ban':
                foreach ($targetUsers as $user) {
                    $user->update(['status' => 'banned']);
                    $user->tokens()->delete();
                }
                $this->log('bulk_banned', null, "Bulk banned {$count} users");
                return back()->with('success', "{$count} users have been banned and blocked successfully.");

            case 'activate':
                foreach ($targetUsers as $user) {
                    $user->update(['status' => 'active']);
                    \App\Models\BannedIp::where('user_id', $user->id)->delete();
                }
                $this->log('bulk_activated', null, "Bulk activated {$count} users");
                return back()->with('success', "{$count} users have been activated successfully.");

            case 'make_vendor':
                foreach ($targetUsers as $user) {
                    $user->update(['role' => 'vendor']);
                }
                $this->log('bulk_promoted_vendor', null, "Bulk promoted {$count} users to Vendor");
                return back()->with('success', "{$count} users have been promoted to Vendor role.");

            case 'make_customer':
                foreach ($targetUsers as $user) {
                    if ($user->role !== 'admin') {
                        $user->update(['role' => 'customer']);
                    }
                }
                $this->log('bulk_demoted_customer', null, "Bulk changed {$count} users to Customer");
                return back()->with('success', "{$count} users have been set to Customer role.");

            case 'delete':
                $today = now()->toDateString();
                $deletedCount = 0;
                $blockedVendors = [];

                foreach ($targetUsers as $user) {
                    if ($user->role === 'admin' || $user->role === 'super_admin') {
                        continue;
                    }

                    if ($user->isVendor()) {
                        $hasActive = Booking::whereHas('property', fn($q) => $q->where('vendor_id', $user->id))
                            ->whereIn('status', ['confirmed', 'pending'])
                            ->where('check_out', '>=', $today)
                            ->exists();

                        if ($hasActive) {
                            $blockedVendors[] = $user->name;
                            continue;
                        }
                    }

                    $user->tokens()->delete();
                    $user->delete();
                    $deletedCount++;
                }

                $this->log('bulk_deleted', null, "Bulk deleted {$deletedCount} user accounts");

                if (!empty($blockedVendors)) {
                    return back()->with('warning', "Deleted {$deletedCount} users. The following vendors were protected due to active reservations: " . implode(', ', $blockedVendors));
                }

                return back()->with('success', "{$deletedCount} user accounts deleted successfully.");

            default:
                return back()->with('error', 'Invalid bulk action specified.');
        }
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
