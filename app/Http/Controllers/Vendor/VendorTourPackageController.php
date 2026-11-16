<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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

        $inclusionsList = array_values(array_filter(array_map('trim', explode("\n", str_replace(',', "\n", $request->input('inclusions', ''))))));
        $highlightsList = array_values(array_filter(array_map('trim', explode("\n", str_replace(',', "\n", $request->input('highlights', ''))))));

        DB::transaction(function () use ($validated, $inclusionsList, $highlightsList) {
            $package = new TourPackage();
            $package->vendor_id        = auth()->id();
            $package->title            = $validated['title'];
            $package->slug             = Str::slug($validated['title']) . '-' . Str::random(5);
            $package->destination      = $validated['destination'];
            $package->duration_days    = $validated['duration_days'];
            $package->duration_nights  = $validated['duration_nights'];
            $package->days             = $validated['duration_days'] . ' Days';
            $package->price_per_person = $validated['price_per_person'];
            $package->price            = $validated['price_per_person'];
            $package->discount_price   = $validated['discount_price'] ?? null;
            $package->featured_image   = $validated['featured_image'];
            $package->image_url        = $validated['featured_image'];
            $package->inclusions       = $inclusionsList;
            $package->includes         = $inclusionsList;
            $package->highlights       = $highlightsList;
            $package->status           = 'pending';
            $package->is_active        = false;
            $package->max_seats        = $validated['max_seats'];
            $package->available_seats  = $validated['max_seats'];
            $package->save();

            Cache::forget('tour_packages_active');
        });

        return redirect()->route('vendor.packages.index')->with('success', 'Tour Package submitted for admin approval! It will go live once approved.');
    }

    public function edit(int $id): View
    {
        $package = TourPackage::where('vendor_id', auth()->id())->findOrFail($id);
        return view('vendor.packages.edit', compact('package'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $package = TourPackage::where('vendor_id', auth()->id())->findOrFail($id);
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'destination'      => 'required|string|max:255',
            'duration_days'    => 'required|integer|min:1',
            'duration_nights'  => 'required|integer|min:0',
            'price_per_person' => 'required|numeric|min:1',
            'discount_price'   => 'nullable|numeric|min:0',
            'featured_image'   => 'nullable|url',
            'inclusions'       => 'nullable|string',
            'highlights'       => 'nullable|string',
            'max_seats'        => 'required|integer|min:1',
        ]);

        $inclusionsList = array_values(array_filter(array_map('trim', explode("\n", str_replace(',', "\n", $request->input('inclusions', ''))))));
        $highlightsList = array_values(array_filter(array_map('trim', explode("\n", str_replace(',', "\n", $request->input('highlights', ''))))));

        DB::transaction(function () use ($package, $validated, $inclusionsList, $highlightsList) {
            $package->update([
                'title'            => $validated['title'],
                'destination'      => $validated['destination'],
                'duration_days'    => $validated['duration_days'],
                'duration_nights'  => $validated['duration_nights'],
                'days'             => $validated['duration_days'] . ' Days',
                'price_per_person' => $validated['price_per_person'],
                'price'            => $validated['price_per_person'],
                'discount_price'   => $validated['discount_price'] ?? null,
                'featured_image'   => $validated['featured_image'] ?? $package->featured_image,
                'image_url'        => $validated['featured_image'] ?? $package->image_url,
                'inclusions'       => !empty($inclusionsList) ? $inclusionsList : $package->inclusions,
                'includes'         => !empty($inclusionsList) ? $inclusionsList : $package->includes,
                'highlights'       => !empty($highlightsList) ? $highlightsList : $package->highlights,
                'max_seats'        => $validated['max_seats'],
            ]);

            Cache::forget('tour_packages_active');
        });

        return redirect()->route('vendor.packages.index')->with('success', 'Tour Package updated successfully.');
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
