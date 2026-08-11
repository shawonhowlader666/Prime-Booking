<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourPackageController extends Controller
{
    /**
     * Show all tour packages with search & destination filters.
     * GET /packages
     */
    public function index(Request $request): View
    {
        $query = TourPackage::active();

        if ($request->filled('destination')) {
            $query->destination($request->string('destination')->trim()->toString());
        }

        if ($request->filled('sort')) {
            match($request->string('sort')->toString()) {
                'price_low'  => $query->orderBy('price_per_person', 'asc'),
                'price_high' => $query->orderBy('price_per_person', 'desc'),
                'duration'   => $query->orderBy('duration_days', 'desc'),
                default      => $query->latest(),
            };
        } else {
            $query->latest();
        }

        $packages = $query->paginate(9);
        $destinations = TourPackage::active()->distinct()->pluck('destination');

        return view('pages.packages.index', compact('packages', 'destinations'));
    }

    /**
     * Show single tour package details.
     * GET /packages/{slug}
     */
    public function show(string $slug): View
    {
        $package = TourPackage::where('slug', $slug)->firstOrFail();
        $relatedPackages = TourPackage::active()
            ->where('id', '!=', $package->id)
            ->where('destination', $package->destination)
            ->limit(3)
            ->get();

        if ($relatedPackages->isEmpty()) {
            $relatedPackages = TourPackage::active()
                ->where('id', '!=', $package->id)
                ->limit(3)
                ->get();
        }

        return view('pages.packages.show', compact('package', 'relatedPackages'));
    }
}
