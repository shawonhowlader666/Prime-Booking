<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourPackage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TourPackageController extends Controller
{
    public function index()
    {
        $packages = TourPackage::with('vendor')->ordered()->paginate(per_page(15));
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'destination'      => 'nullable|string|max:255',
            'days'             => 'nullable|string|max:50',
            'duration_days'    => 'nullable|integer|min:1',
            'duration_nights'  => 'nullable|integer|min:0',
            'price'            => 'nullable|numeric|min:0',
            'price_per_person' => 'nullable|numeric|min:0',
            'discount_price'   => 'nullable|numeric|min:0',
            'badge'            => 'nullable|string|max:50',
            'image_url'        => 'nullable|url|max:500',
            'featured_image'   => 'nullable|url|max:500',
            'includes'         => 'nullable|string',
            'inclusions'       => 'nullable|string',
            'highlights'       => 'nullable|string',
            'description'      => 'nullable|string',
            'is_active'        => 'nullable|boolean',
            'is_featured'      => 'nullable|boolean',
            'sort_order'       => 'nullable|integer|min:0',
            'max_seats'        => 'nullable|integer|min:1',
        ]);

        $price = $validated['price'] ?? $validated['price_per_person'] ?? 0;
        $durationDays = isset($validated['duration_days']) ? (int)$validated['duration_days'] : null;
        if (!$durationDays && !empty($validated['days'])) {
            $num = (int) filter_var($validated['days'], FILTER_SANITIZE_NUMBER_INT);
            $durationDays = $num > 0 ? $num : 1;
        }
        $durationDays = $durationDays ?: 1;
        $days = !empty($validated['days']) ? $validated['days'] : ($durationDays . ' Days');
        $durationNights = isset($validated['duration_nights']) ? (int)$validated['duration_nights'] : max(0, $durationDays - 1);

        $imageUrl = $validated['featured_image'] ?? $validated['image_url'] ?? null;
        $destination = $validated['destination'] ?? 'Bangladesh';

        $includesRaw = $validated['inclusions'] ?? $validated['includes'] ?? '';
        if (is_string($includesRaw) && trim($includesRaw) !== '') {
            $includesArr = array_values(array_filter(array_map('trim', explode("\n", str_replace(',', "\n", $includesRaw)))));
        } else {
            $includesArr = [];
        }

        $highlightsRaw = $validated['highlights'] ?? '';
        if (is_string($highlightsRaw) && trim($highlightsRaw) !== '') {
            $highlightsArr = array_values(array_filter(array_map('trim', explode("\n", str_replace(',', "\n", $highlightsRaw)))));
        } else {
            $highlightsArr = [];
        }

        $isActive = $request->has('is_active') ? true : ($request->input('status') === 'inactive' ? false : true);
        $status = $isActive ? 'active' : 'inactive';

        $packageData = [
            'vendor_id'        => auth()->id(),
            'title'            => $validated['title'],
            'slug'             => Str::slug($validated['title']) . '-' . Str::random(5),
            'destination'      => $destination,
            'days'             => $days,
            'duration_days'    => $durationDays,
            'duration_nights'  => $durationNights,
            'price'            => (float)$price,
            'price_per_person' => (float)$price,
            'discount_price'   => isset($validated['discount_price']) ? (float)$validated['discount_price'] : null,
            'badge'            => $validated['badge'] ?? null,
            'image_url'        => $imageUrl,
            'featured_image'   => $imageUrl,
            'includes'         => $includesArr,
            'inclusions'       => $includesArr,
            'highlights'       => $highlightsArr,
            'description'      => $validated['description'] ?? null,
            'is_active'        => $isActive,
            'status'           => $status,
            'is_featured'      => $request->has('is_featured'),
            'sort_order'       => $validated['sort_order'] ?? 0,
            'max_seats'        => $validated['max_seats'] ?? 20,
            'available_seats'  => $validated['max_seats'] ?? 20,
        ];

        DB::transaction(function () use ($packageData) {
            TourPackage::create($packageData);
            Cache::forget('tour_packages_active');
        });

        return redirect()->route('admin.packages.index')->with('success', 'Tour Package created successfully!');
    }

    public function edit(TourPackage $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, TourPackage $package)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'destination'      => 'nullable|string|max:255',
            'days'             => 'nullable|string|max:50',
            'duration_days'    => 'nullable|integer|min:1',
            'duration_nights'  => 'nullable|integer|min:0',
            'price'            => 'nullable|numeric|min:0',
            'price_per_person' => 'nullable|numeric|min:0',
            'discount_price'   => 'nullable|numeric|min:0',
            'badge'            => 'nullable|string|max:50',
            'image_url'        => 'nullable|url|max:500',
            'featured_image'   => 'nullable|url|max:500',
            'includes'         => 'nullable|string',
            'inclusions'       => 'nullable|string',
            'highlights'       => 'nullable|string',
            'description'      => 'nullable|string',
            'is_active'        => 'nullable|boolean',
            'is_featured'      => 'nullable|boolean',
            'sort_order'       => 'nullable|integer|min:0',
            'max_seats'        => 'nullable|integer|min:1',
        ]);

        $price = $validated['price'] ?? $validated['price_per_person'] ?? $package->price_per_person;
        $durationDays = isset($validated['duration_days']) ? (int)$validated['duration_days'] : null;
        if (!$durationDays && !empty($validated['days'])) {
            $num = (int) filter_var($validated['days'], FILTER_SANITIZE_NUMBER_INT);
            $durationDays = $num > 0 ? $num : 1;
        }
        $durationDays = $durationDays ?: ($package->duration_days ?: 1);
        $days = !empty($validated['days']) ? $validated['days'] : ($durationDays . ' Days');
        $durationNights = isset($validated['duration_nights']) ? (int)$validated['duration_nights'] : max(0, $durationDays - 1);

        $imageUrl = $validated['featured_image'] ?? $validated['image_url'] ?? $package->featured_image;
        $destination = $validated['destination'] ?? ($package->destination ?: 'Bangladesh');

        $includesRaw = $validated['inclusions'] ?? $validated['includes'] ?? null;
        if ($includesRaw !== null) {
            if (is_string($includesRaw) && trim($includesRaw) !== '') {
                $includesArr = array_values(array_filter(array_map('trim', explode("\n", str_replace(',', "\n", $includesRaw)))));
            } else {
                $includesArr = [];
            }
        } else {
            $includesArr = $package->inclusions ?? $package->includes ?? [];
        }

        $highlightsRaw = $validated['highlights'] ?? null;
        if ($highlightsRaw !== null) {
            if (is_string($highlightsRaw) && trim($highlightsRaw) !== '') {
                $highlightsArr = array_values(array_filter(array_map('trim', explode("\n", str_replace(',', "\n", $highlightsRaw)))));
            } else {
                $highlightsArr = [];
            }
        } else {
            $highlightsArr = $package->highlights ?? [];
        }

        $isActive = $request->has('is_active') ? true : ($request->input('status') === 'inactive' ? false : $package->is_active);
        $status = $isActive ? 'active' : 'inactive';

        $packageData = [
            'title'            => $validated['title'],
            'destination'      => $destination,
            'days'             => $days,
            'duration_days'    => $durationDays,
            'duration_nights'  => $durationNights,
            'price'            => (float)$price,
            'price_per_person' => (float)$price,
            'discount_price'   => isset($validated['discount_price']) ? (float)$validated['discount_price'] : $package->discount_price,
            'badge'            => $validated['badge'] ?? $package->badge,
            'image_url'        => $imageUrl,
            'featured_image'   => $imageUrl,
            'includes'         => $includesArr,
            'inclusions'       => $includesArr,
            'highlights'       => $highlightsArr,
            'description'      => $validated['description'] ?? $package->description,
            'is_active'        => $isActive,
            'status'           => $status,
            'is_featured'      => $request->has('is_featured'),
            'sort_order'       => $validated['sort_order'] ?? $package->sort_order,
        ];

        if (isset($validated['max_seats'])) {
            $packageData['max_seats'] = (int)$validated['max_seats'];
        }

        DB::transaction(function () use ($package, $packageData) {
            $package->update($packageData);
            Cache::forget('tour_packages_active');
        });

        return redirect()->route('admin.packages.index')->with('success', 'Tour Package updated successfully!');
    }

    public function destroy(TourPackage $package)
    {
        // Security Guard: Prevent deleting tour packages with active or upcoming traveler bookings
        $hasActive = \App\Models\PackageBooking::where('package_id', $package->id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('travel_date', '>=', now()->toDateString())
            ->exists();

        if ($hasActive) {
            return back()->with('error', "Cannot delete package \"{$package->title}\" because it has active or upcoming traveler reservations. Please fulfill or cancel reservations first.");
        }

        $package->delete();
        Cache::forget('tour_packages_active');
        return redirect()->route('admin.packages.index')->with('success', 'Tour Package deleted successfully!');
    }
}
