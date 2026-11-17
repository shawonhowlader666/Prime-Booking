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

        $tenants = Tenant::with('user:id,email,name')->withCount(['properties', 'rooms'])->latest()->paginate(15);

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

