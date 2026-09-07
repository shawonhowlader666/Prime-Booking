<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Services\SaaSTenantManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTenantManagementController extends Controller
{
    public function __construct(
        protected SaaSTenantManagementService $tenantService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status', 'plan']);
        $metrics = $this->tenantService->getTenantMetrics();
        $tenants = $this->tenantService->getTenants($filters, 15);

        return view('pages.admin-tenants', compact('metrics', 'tenants', 'filters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:hotels,code',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:10',
            'subscription_plan' => 'required|string|in:TRIAL,STANDARD,PREMIUM,ENTERPRISE',
            'monthly_fee' => 'nullable|numeric|min:0',
            'is_trial' => 'nullable|boolean',
            'trial_days' => 'nullable|integer|min:1|max:365',
            'owner_name' => 'nullable|string|max:255',
            'owner_email' => 'required|email|max:255|unique:users,email',
            'owner_password' => 'required|string|min:6',
        ]);

        $result = $this->tenantService->createTenant($validated);

        return redirect()->route('admin.tenants.index')->with('success', "Hotel '{$result['hotel']->name}' created successfully with Owner email '{$result['owner']->email}'.");
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $hotel = Hotel::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'subscription_plan' => 'required|string|in:TRIAL,STANDARD,PREMIUM,ENTERPRISE',
            'subscription_status' => 'required|string|in:ACTIVE,TRIAL,EXPIRED,SUSPENDED',
            'monthly_fee' => 'nullable|numeric|min:0',
            'subscription_expires_at' => 'nullable|date',
            'is_subscription_locked' => 'nullable|boolean',
            'prime_booking_property_id' => 'nullable|string',
        ]);

        $this->tenantService->updateTenant($hotel, $validated);

        return redirect()->route('admin.tenants.index')->with('success', "Hotel '{$hotel->name}' updated successfully.");
    }

    public function toggle(Request $request, int $id): RedirectResponse
    {
        $hotel = Hotel::findOrFail($id);
        $action = $request->input('action', 'ACTIVATE');

        $this->tenantService->toggleStatus($hotel, $action);

        return redirect()->back()->with('success', "Hotel '{$hotel->name}' status updated to {$action}.");
    }

    public function extend(Request $request, int $id): RedirectResponse
    {
        $hotel = Hotel::findOrFail($id);
        $days = (int) $request->input('days', 30);

        $this->tenantService->extendSubscription($hotel, $days);

        return redirect()->back()->with('success', "Hotel '{$hotel->name}' subscription extended by {$days} days.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $hotel = Hotel::findOrFail($id);
        $name = $hotel->name;

        $this->tenantService->deleteTenant($hotel);

        return redirect()->route('admin.tenants.index')->with('success', "Hotel '{$name}' and all associated records deleted permanently.");
    }

    public function impersonate(int $id): RedirectResponse
    {
        $hotel = Hotel::findOrFail($id);
        session(['active_hotel_id' => $hotel->id]);

        return redirect()->route('dashboard')->with('success', "Switched into active context for '{$hotel->name}'.");
    }
}
