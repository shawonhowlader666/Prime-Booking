<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Review;
use App\Services\RecommendationService;
use App\Services\SeoSchemaService;
use App\Services\SocialProofService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class PropertyPreviewController
 *
 * Provides live Agoda-grade preview of any property listing (active, pending, inactive, or rejected)
 * to authenticated Admins and Property Owners (Vendors).
 */
class PropertyPreviewController extends Controller
{
    /**
     * Preview property detail page.
     * Accessible by: Super Admin, Admin, or the Vendor who owns the property.
     */
    public function preview(Request $request, int|string $id): View|RedirectResponse
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to preview properties.');
        }

        // Query property without the active() constraint
        $query = Property::with([
            'rooms' => fn($q) => $q->orderBy('price_per_night'),
            'location',
            'vendor',
            'reviews' => fn($q) => $q->latest(),
        ]);

        if (is_numeric($id)) {
            $property = $query->where('id', (int) $id)->first();
        } else {
            $property = $query->where('slug', (string) $id)->first();
        }

        if (!$property) {
            abort(404, 'Property listing not found for preview.');
        }

        // Authorization check: Admin/Super Admin or Property Owner
        $isAdmin = in_array($user->role, ['admin', 'super_admin'], true);
        $isOwner = (int) $property->vendor_id === (int) $user->id;

        if (!$isAdmin && !$isOwner) {
            abort(403, 'Unauthorized. You do not have permission to preview this property listing.');
        }

        // Recommendations & Similar Properties
        $recommendationService = app(RecommendationService::class);
        $related = $recommendationService->getSimilarProperties($property, 4);

        // Social Proof Signals
        $socialProofService = app(SocialProofService::class);
        $socialProof = $socialProofService->getSignals($property);

        // SEO Schema
        $seoSchemaService = app(SeoSchemaService::class);
        $seoSchema = $seoSchemaService->generateHotelSchema($property);

        // Reviews
        $reviews = Review::where('property_id', $property->id)
            ->with('user:id,name,avatar')
            ->latest()
            ->limit(10)
            ->get();

        $checkIn  = $request->query('check_in', now()->format('Y-m-d'));
        $checkOut = $request->query('check_out', now()->addDays(2)->format('Y-m-d'));
        $guests   = (int) $request->query('guests', 2);
        $nights   = max(1, (int) Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)));

        return view('pages.hotel-detail', [
            'property'        => $property,
            'related'         => $related,
            'socialProof'     => $socialProof,
            'seoSchema'       => $seoSchema,
            'reviews'         => $reviews,
            'checkIn'         => $checkIn,
            'checkOut'        => $checkOut,
            'guests'          => $guests,
            'nights'          => $nights,
            'isPreview'       => true,
            'previewUserRole' => $user->role,
            'isAdminPreview'  => $isAdmin,
        ]);
    }
}
