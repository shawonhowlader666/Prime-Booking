<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourPackage;
use Illuminate\Support\Facades\Cache;

class TourPackageController extends Controller
{
    public function index()
    {
        $packages = TourPackage::with('vendor')->ordered()->paginate(15);
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'days'        => 'required|string|max:50',
            'price'       => 'required|numeric|min:0',
            'badge'       => 'nullable|string|max:50',
            'image_url'   => 'nullable|url|max:500',
            'includes'    => 'nullable|string', // comma or newline separated from form
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        if (!empty($validated['includes']) && is_string($validated['includes'])) {
            $validated['includes'] = array_values(array_filter(array_map('trim', explode("\n", str_replace(',', "\n", $validated['includes'])))));
        } else {
            $validated['includes'] = [];
        }

        $validated['is_active']   = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['sort_order']  = $validated['sort_order'] ?? 0;

        TourPackage::create($validated);
        Cache::forget('tour_packages_active');

        return redirect()->route('admin.packages.index')->with('success', 'Tour Package created successfully!');
    }

    public function edit(TourPackage $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, TourPackage $package)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'days'        => 'required|string|max:50',
            'price'       => 'required|numeric|min:0',
            'badge'       => 'nullable|string|max:50',
            'image_url'   => 'nullable|url|max:500',
            'includes'    => 'nullable|string',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        if (!empty($validated['includes']) && is_string($validated['includes'])) {
            $validated['includes'] = array_values(array_filter(array_map('trim', explode("\n", str_replace(',', "\n", $validated['includes'])))));
        } else {
            $validated['includes'] = [];
        }

        $validated['is_active']   = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['sort_order']  = $validated['sort_order'] ?? 0;

        $package->update($validated);
        Cache::forget('tour_packages_active');

        return redirect()->route('admin.packages.index')->with('success', 'Tour Package updated successfully!');
    }

    public function destroy(TourPackage $package)
    {
        $package->delete();
        Cache::forget('tour_packages_active');
        return redirect()->route('admin.packages.index')->with('success', 'Tour Package deleted!');
    }
}
