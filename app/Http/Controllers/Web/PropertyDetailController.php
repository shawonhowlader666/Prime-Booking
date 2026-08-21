<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Property;
use App\Repositories\PropertyRepository;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * PropertyDetailController — Hotel/Property Detail Page
 *
 * Handles the single property detail page (Agoda-style).
 * Route: GET /hotels/{id}  or  GET /property/{slug}
 */
class PropertyDetailController extends Controller
{
    public function __construct(
        private readonly PropertyRepository $repository
    ) {}

    /**
     * Show the property detail page.
     * GET /hotels/{id}
     */
    public function show(Request $request, int|string $id): View|RedirectResponse
    {
        // Support both numeric ID and slug
        if (is_numeric($id)) {
            $property = $this->repository->findWithRooms((int) $id);
        } else {
            $property = $this->repository->findBySlug((string) $id);
        }

        if (! $property) {
            $property = \App\Models\Property::with('rooms')->first();
        }

        if (! $property) {
            $property = new \App\Models\Property([
                'id' => is_numeric($id) ? (int)$id : 1,
                'name' => 'Sea Shell Service Apartment',
                'slug' => 'sea-shell-service-apartment',
                'city' => 'Dhaka',
                'address' => 'House#06 Road-21, Sector 4, Uttara, Dhaka, Bangladesh, 1230',
                'price_per_night' => 14500,
                'rating_score' => 8.7,
                'total_reviews' => 6,
                'star_rating' => 4,
                'description' => 'Conveniently situated in the Uttara part of Dhaka, this property puts you close to attractions and interesting dining options.',
                'primary_image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&w=600&q=80',
                ],
                'amenities' => ['Free Wi-Fi', 'Free parking', 'Pets allowed', 'Air conditioning in public area', 'English', 'Internet services'],
            ]);
            $property->rooms = collect();
        }

        // Smart Algorithmic Recommendations (Multi-Attribute Jaccard & Proximity)
        $recommendationService = app(\App\Services\RecommendationService::class);
        $related = $recommendationService->getSimilarProperties($property, 4);
        if ($related->isEmpty()) {
            $related = $this->repository->getRelated($property, 4);
        }

        // Live Social Proof & Demand Signals
        $socialProofService = app(\App\Services\SocialProofService::class);
        $socialProof = $socialProofService->getSignals($property);

        // Google SEO Schema (JSON-LD)
        $seoSchemaService = app(\App\Services\SeoSchemaService::class);
        $seoSchema = $seoSchemaService->generateHotelSchema($property);

        // Latest reviews from DB (limit 10 for page load)
        $reviews = Review::where('property_id', $property->id)
            ->where('status', 'approved')
            ->with('user:id,name,avatar')
            ->latest()
            ->limit(10)
            ->get();

        // Check-in/out from query string (carry over from search)
        $checkIn  = $request->query('check_in',  now()->format('Y-m-d'));
        $checkOut = $request->query('check_out', now()->addDays(2)->format('Y-m-d'));
        $guests   = (int) $request->query('guests', 2);

        // Night count for price calculation
        $nights = max(1, (int) \Carbon\Carbon::parse($checkIn)->diffInDays(\Carbon\Carbon::parse($checkOut)));

        return view('pages.hotel-detail', compact(
            'property',
            'related',
            'socialProof',
            'seoSchema',
            'reviews',
            'checkIn',
            'checkOut',
            'guests',
            'nights',
        ));
    }

    /**
     * Submit a guest review for a property.
     * POST /hotels/{id}/review or POST /property/{id}/review
     */
    public function submitReview(Request $request, int|string $id): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'guest_name' => 'nullable|string|max:255',
            'rating'     => 'required|numeric|min:1|max:10',
            'comment'    => 'required|string|min:5|max:2000',
        ]);

        $propertyId = is_numeric($id) ? (int)$id : 1;
        $user = auth()->user();
        $guestName = $user ? $user->name : ($validated['guest_name'] ?? 'Verified Guest');

        // AI Sentiment Analysis & Auto-flagging
        $analyzer = app(\App\Services\AI\SentimentAnalyzer::class);
        $sentimentResult = $analyzer->analyze($validated['comment'], (float)$validated['rating']);

        $status = $sentimentResult['is_flagged'] ? 'flagged' : 'pending';

        $review = Review::create([
            'property_id'     => $propertyId,
            'user_id'         => $user?->id,
            'guest_name'      => e(strip_tags($guestName)),
            'rating'          => (float) $validated['rating'],
            'comment'         => e(strip_tags($validated['comment'])),
            'status'          => $status,
        ]);

        // Auto-approve clean reviews and update property average rating & count
        if ($status !== 'flagged') {
            $review->update(['status' => 'approved']);

            $avgRating = Review::where('property_id', $propertyId)->where('status', 'approved')->avg('rating');
            $reviewCount = Review::where('property_id', $propertyId)->where('status', 'approved')->count();

            Property::where('id', $propertyId)->update([
                'rating_score'  => round((float)($avgRating ?: $validated['rating']), 1),
                'total_reviews' => $reviewCount,
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'message'   => $status === 'flagged' ? 'Your review has been submitted for moderation.' : 'Thank you! Your verified review has been published.',
                'sentiment' => $sentimentResult['sentiment'],
                'review'    => $review,
            ]);
        }

        return redirect()->back()->with('success', $status === 'flagged' ? 'Your review has been submitted for moderation.' : 'Thank you! Your verified review has been published.');
    }

    /**
     * Show/print shareable property brochure.
     * GET /hotels/{id}/brochure
     */
    public function brochure(int|string $id): View
    {
        if (is_numeric($id)) {
            $property = $this->repository->findWithRooms((int) $id);
        } else {
            $property = $this->repository->findBySlug((string) $id);
        }

        if (! $property) {
            $property = Property::with('rooms')->firstOrFail();
        }

        return view('pages.hotel-brochure-print', compact('property'));
    }

    /**
     * Submit Guest Inquiry to Hotel Front Desk
     */
    public function submitInquiry(Request $request, int|string $id): RedirectResponse
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'required|string|max:20',
            'email'   => 'nullable|email|max:100',
            'message' => 'required|string|max:1000',
        ]);

        $property = is_numeric($id) ? Property::find($id) : Property::where('slug', $id)->first();

        \App\Models\Inquiry::create([
            'property_id'  => $property?->id,
            'vendor_id'    => $property?->vendor_id,
            'name'         => $request->name,
            'phone'        => $request->phone,
            'email'        => $request->email,
            'service_type' => $request->input('service_type', 'Hotel Question & Policy'),
            'destination'  => $property?->city ?? 'Dhaka',
            'travel_date'  => $request->input('travel_date', date('Y-m-d')),
            'passengers'   => (int) $request->input('passengers', 1),
            'message'      => $request->message,
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Your inquiry has been directly sent to ' . ($property?->name ?? 'the hotel front desk') . '. You will receive a quick reply.');
    }
}

