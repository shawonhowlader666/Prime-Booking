<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VendorTourPackageController extends Controller
{
    /**
     * Show vendor's tour packages ledger.
     * GET /vendor/packages
     */
    public function index(): View
    {
        $vendorId = auth()->id();
        $packages = TourPackage::where('vendor_id', $vendorId)->latest()->paginate(10);

        return view('vendor.packages.index', compact('packages'));
    }

    /**
     * Show create tour package form.
     * GET /vendor/packages/create
     */
    public function create(): View
    {
        return view('vendor.packages.create');
    }

    /**
     * Store a new vendor tour package.
     * POST /vendor/packages
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'destination'      => 'required|string|max:255',
            'duration_days'    => 'required|integer|min:1',
            'duration_nights'  => 'required|integer|min:0',
            'price_per_person' => 'required|numeric|min:1',
            'discount_price'   => 'nullable|numeric|min:0',
            'featured_image'   => 'required|url',
            'inclusions'       => 'nullable|string', // Comma separated or textarea
            'highlights'       => 'nullable|string',
            'max_seats'        => 'required|integer|min:1',
        ]);

        $inclusionsList = array_filter(array_map('trim', explode("\n", $request->input('inclusions', ''))));
        $highlightsList = array_filter(array_map('trim', explode("\n", $request->input('highlights', ''))));

        $package = new TourPackage();
        $package->vendor_id        = auth()->id();
        $package->title            = $validated['title'];
        $package->slug             = Str::slug($validated['title']) . '-' . Str::random(5);
        $package->destination      = $validated['destination'];
        $package->duration_days    = $validated['duration_days'];
        $package->duration_nights  = $validated['duration_nights'];
        $package->price_per_person = $validated['price_per_person'];
        $package->discount_price   = $validated['discount_price'] ?? null;
        $package->featured_image   = $validated['featured_image'];
        $package->inclusions       = $inclusionsList;
        $package->highlights       = $highlightsList;
        $package->status           = 'active';
        $package->max_seats        = $validated['max_seats'];
        $package->available_seats  = $validated['max_seats'];
        $package->save();

        return redirect()->route('vendor.packages.index')->with('success', 'Tour Package created and live successfully!');
    }

    /**
     * Delete a vendor package.
     * DELETE /vendor/packages/{id}
     */
    public function destroy(int $id): RedirectResponse
    {
        $package = TourPackage::where('vendor_id', auth()->id())->findOrFail($id);
        $package->delete();

        return redirect()->route('vendor.packages.index')->with('success', 'Package deleted successfully.');
    }
}
