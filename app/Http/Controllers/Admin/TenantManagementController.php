<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;

class TenantManagementController extends Controller
{
    public function index()
    {
        $company = config('company');

        // Seed default sample tenants if DB table is empty
        if (Tenant::count() === 0) {
            Tenant::create([
                'name' => 'Royal Tulip Resort Group',
                'owner_name' => 'Kazi Tanvir',
                'email' => 'vendor@royaltulip.com',
                'phone' => '01711223344',
                'saas_plan' => 'Enterprise',
                'commission_rate' => 5.00,
                'status' => 'active',
                'notes' => '5-star luxury resort partner in Cox\'s Bazar',
            ]);
            Tenant::create([
                'name' => 'Sundarban Cruise Line Ltd',
                'owner_name' => 'Captain Rafiq',
                'email' => 'vendor@sundarbancruise.com',
                'phone' => '01819887766',
                'saas_plan' => 'Pro Partner',
                'commission_rate' => 8.00,
                'status' => 'active',
                'notes' => 'Premium ship cruise operator in Khulna & Mongla',
            ]);
            Tenant::create([
                'name' => 'Sajek Eco Cottages Association',
                'owner_name' => 'Chakma Travel',
                'email' => 'vendor@sajekeco.com',
                'phone' => '01912334455',
                'saas_plan' => 'Starter',
                'commission_rate' => 12.00,
                'status' => 'active',
                'notes' => 'Eco-resorts and mountain cottages group in Rangamati',
            ]);
        }

        $tenants = Tenant::withCount(['properties', 'rooms'])->latest()->paginate(15);

        return view('admin.tenants.index', compact('company', 'tenants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'owner_name'      => 'nullable|string|max:255',
            'email'           => 'required|email|unique:tenants,email',
            'phone'           => 'nullable|string|max:50',
            'saas_plan'       => 'required|string|max:100',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'notes'           => 'nullable|string',
        ]);

        Tenant::create($validated + ['status' => 'active']);

        return back()->with('success', 'SaaS Partner / Tenant created successfully!');
    }

    public function update(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'owner_name'      => 'nullable|string|max:255',
            'email'           => 'required|email|unique:tenants,email,' . $id,
            'phone'           => 'nullable|string|max:50',
            'saas_plan'       => 'required|string|max:100',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status'          => 'required|in:active,suspended,pending',
            'notes'           => 'nullable|string',
        ]);

        $tenant->update($validated);

        return back()->with('success', 'Tenant details and commission updated successfully!');
    }

    public function toggleStatus($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->status = ($tenant->status === 'active') ? 'suspended' : 'active';
        $tenant->save();

        return back()->with('success', "Tenant status changed to {$tenant->status}!");
    }

    public function destroy($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete();

        return back()->with('success', 'Tenant deleted successfully!');
    }
}

