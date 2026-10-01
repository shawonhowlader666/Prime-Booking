<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminTourPackageController extends Controller
{
    /**
     * Show all tour packages in system.
     * GET /admin/packages
     */
    public function index(): View
    {
        $packages = TourPackage::with('vendor')->latest()->paginate(15);
        $totalCount = TourPackage::count();
        $activeCount = TourPackage::where('status', 'active')->count();

        return view('admin.packages.index', compact('packages', 'totalCount', 'activeCount'));
    }

    /**
     * Show all tour package reservations.
     * GET /admin/packages/bookings
     */
    public function bookings(): View
    {
        $bookings = \App\Models\PackageBooking::with('package', 'user')->latest()->paginate(20);
        return view('admin.packages.bookings', compact('bookings'));
    }

    /**
     * Approve or change package status.
     * POST /admin/packages/{id}/status
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $package = TourPackage::findOrFail($id);
        $package->status = ($package->status === 'active') ? 'inactive' : 'active';
        $package->save();

        return redirect()->back()->with('success', "Package {$package->title} status updated to {$package->status}.");
    }

    /**
     * Delete a package from system.
     * DELETE /admin/packages/{id}
     */
    public function destroy(int $id): RedirectResponse
    {
        $package = TourPackage::findOrFail($id);
        $package->delete();

        return redirect()->back()->with('success', 'Package removed from system.');
    }
}
