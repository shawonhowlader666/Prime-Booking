<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourPackage;
use Illuminate\Support\Facades\Cache;

class VendorPackageController extends Controller
{
    public function index()
    {
        $vendorId = auth()->id();
        $packages = TourPackage::where('vendor_id', $vendorId)->latest()->paginate(10);
        return view('vendor.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('vendor.packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'days'        => 'required|string|max:50',
            'price'       => 'required|numeric|min:0',
            'badge'       => 'nullable|string|max:50',
            'image_url'   => 'nullable|url|max:500',
            'includes'    => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if (!empty($validated['includes']) && is_string($validated['includes'])) {
            $validated['includes'] = array_values(array_filter(array_map('trim', explode("\n", str_replace(',', "\n", $validated['includes'])))));
        } else {
            $validated['includes'] = [];
        }

        $validated['vendor_id'] = auth()->id();
        $validated['is_active'] = true;

        TourPackage::create($validated);
        Cache::forget('tour_packages_active');

        return redirect()->route('vendor.packages.index')->with('success', 'Tour Package submitted successfully!');
    }

    public function edit($id)
    {
        $package = TourPackage::where('vendor_id', auth()->id())->where('id', $id)->firstOrFail();
        return view('vendor.packages.edit', compact('package'));
    }

    public function update(Request $request, $id)
    {
        $package = TourPackage::where('vendor_id', auth()->id())->where('id', $id)->firstOrFail();

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'days'        => 'required|string|max:50',
            'price'       => 'required|numeric|min:0',
            'badge'       => 'nullable|string|max:50',
            'image_url'   => 'nullable|url|max:500',
            'includes'    => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if (!empty($validated['includes']) && is_string($validated['includes'])) {
            $validated['includes'] = array_values(array_filter(array_map('trim', explode("\n", str_replace(',', "\n", $validated['includes'])))));
        } else {
            $validated['includes'] = [];
        }

        $package->update($validated);
        Cache::forget('tour_packages_active');

        return redirect()->route('vendor.packages.index')->with('success', 'Tour Package updated successfully!');
    }

    public function destroy($id)
    {
        $package = TourPackage::where('vendor_id', auth()->id())->where('id', $id)->firstOrFail();
        $package->delete();
        Cache::forget('tour_packages_active');

        return redirect()->route('vendor.packages.index')->with('success', 'Tour Package deleted!');
    }
}
