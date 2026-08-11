<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VendorReviewController extends Controller
{
    /**
     * Display guest reviews for properties owned by this vendor.
     * GET /vendor/reviews
     */
    public function index(): View
    {
        $vendorId = auth()->id();

        $reviews = Review::whereHas('property', fn($q) => $q->where('vendor_id', $vendorId))
            ->with(['property:id,name,city,primary_image', 'user:id,name'])
            ->latest()
            ->paginate(10);

        return view('vendor.reviews.index', compact('reviews'));
    }

    /**
     * Submit a vendor response reply to a guest review.
     * POST /vendor/reviews/{reviewId}/reply
     */
    public function reply(Request $request, int $reviewId): RedirectResponse
    {
        $vendorId = auth()->id();

        $validated = $request->validate([
            'vendor_reply' => 'required|string|max:1000',
        ]);

        $review = Review::whereHas('property', fn($q) => $q->where('vendor_id', $vendorId))
            ->findOrFail($reviewId);

        $review->vendor_reply    = trim($validated['vendor_reply']);
        $review->replied_at      = now();
        $review->save();

        return back()->with('success', 'Vendor response reply posted to guest review!');
    }
}
