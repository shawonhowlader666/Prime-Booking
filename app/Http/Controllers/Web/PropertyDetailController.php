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
        $property = $this->resolveProperty($id);

        // An unknown hotel is a 404. Showing some other hotel instead misleads guests and search engines.
        abort_if(! $property, 404);

        // /hotels/{id} and /property/{slug} are aliases: send everything to one canonical URL.
        if (! is_numeric($id) && $property->slug && $property->slug !== (string) $id) {
            return redirect()->route('hotel.show', ['slug' => $property->slug] + $request->query(), 301);
        }

        // Only rooms that are actually on sale.
        if ($property->relationLoaded('rooms')) {
            $property->setRelation('rooms', $property->rooms->filter(
                fn ($room) => ($room->status ?? 'active') === 'active'
            )->sortBy('price_per_night')->values());
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

        // Stay dates carried over from search. Anything unparseable or in the past falls back to sensible defaults.
        [$checkIn, $checkOut] = $this->stayDates($request->query('check_in'), $request->query('check_out'));
        $guests = min(40, max(1, (int) $request->query('guests', $request->query('adults', 2))));
        $nights = max(1, (int) \Carbon\Carbon::parse($checkIn)->diffInDays(\Carbon\Carbon::parse($checkOut)));

        // Real availability for the selected dates, per room type.
        $inventory = app(\App\Services\InventoryService::class);
        $pricing   = app(\App\Services\DynamicPricingService::class);
        $roomAvailability = [];
        foreach ($property->rooms as $room) {
            if ($room->exists) {
                $check = $inventory->checkAvailability((int) $room->id, $checkIn, $checkOut, 1);
                $stay  = $pricing->calculateStayPrice($room, $checkIn, $checkOut, 1);
                $roomAvailability[$room->id] = [
                    'available'    => (bool) $check['is_available'],
                    'rooms_left'   => (int) ($check['min_available'] ?? 0),
                    'nightly_rate' => (float) $stay['avg_nightly_rate'],
                    'stay_total'   => (float) $stay['final_price'],
                ];
            }
        }
        view()->share('roomAvailability', $roomAvailability);
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
            'roomAvailability',
        ));
    }

    /**
     * Submit a guest review for a property.
     * POST /hotels/{id}/review or POST /property/{id}/review
     */
    public function submitReview(Request $request, int|string $id): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:2000',
        ]);

        $fail = function (string $message, int $status = 403) use ($request) {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], $status)
                : redirect()->back()->with('error', $message);
        };

        $user = $request->user();
        if (! $user) {
            return $fail('Please sign in to review a stay.', 401);
        }

        $property = is_numeric($id) ? Property::find((int) $id) : Property::where('slug', (string) $id)->first();
        if (! $property) {
            return $fail('Property not found.', 404);
        }

        // Reviews come only from guests who actually stayed: a confirmed or completed
        // booking at this property whose check-in date has arrived.
        $stayed = \App\Models\Booking::where('property_id', $property->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('check_in', '<=', now()->toDateString())
            ->exists();

        if (! $stayed) {
            return $fail('Only guests who have stayed at this property can review it.');
        }

        if (Review::where('property_id', $property->id)->where('user_id', $user->id)->exists()) {
            return $fail('You have already reviewed this property.', 422);
        }

        $comment = trim(strip_tags($validated['comment']));

        $sentiment = app(\App\Services\AI\SentimentAnalyzer::class)->analyze($comment, (float) $validated['rating'] * 2);
        $status    = ! empty($sentiment['is_flagged']) ? 'flagged' : 'approved';

        // Saving the review recalculates the property's score and review count (Review::booted).
        $review = Review::create([
            'property_id' => $property->id,
            'user_id'     => $user->id,
            'guest_name'  => $user->name,
            'rating'      => (int) $validated['rating'],
            'comment'     => $comment,
            'status'      => $status,
        ]);

        $message = $status === 'flagged'
            ? 'Your review has been submitted for moderation.'
            : 'Thank you! Your verified review has been published.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'message'   => $message,
                'sentiment' => $sentiment['sentiment'] ?? null,
                'review'    => $review->only(['id', 'rating', 'comment', 'status', 'guest_name']),
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /** Active property by numeric id or slug, with its rooms. */
    private function resolveProperty(int|string $id): ?Property
    {
        return is_numeric($id)
            ? $this->repository->findWithRooms((int) $id)
            : $this->repository->findBySlug((string) $id);
    }

    /** @return array{0:string,1:string} check-in / check-out as Y-m-d */
    private function stayDates(mixed $in, mixed $out): array
    {
        $parse = function (mixed $value): ?\Carbon\Carbon {
            if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return null;
            }
            try {
                return \Carbon\Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
            } catch (\Throwable $e) {
                return null;
            }
        };

        $checkIn  = $parse($in);
        $checkOut = $parse($out);
        $today    = now()->startOfDay();

        if (! $checkIn || $checkIn->lt($today)) {
            $checkIn = $today->copy();
        }
        if (! $checkOut || $checkOut->lte($checkIn)) {
            $checkOut = $checkIn->copy()->addDays($out ? 1 : 2);
        }
        if ($checkIn->diffInDays($checkOut) > 60) {
            $checkOut = $checkIn->copy()->addDays(60);
        }

        return [$checkIn->toDateString(), $checkOut->toDateString()];
    }

    /**
     * Show/print shareable property brochure.
     * GET /hotels/{id}/brochure
     */
    public function brochure(int|string $id): View
    {
        $property = $this->resolveProperty($id);
        abort_if(! $property, 404);

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

        $property = is_numeric($id) ? Property::active()->find((int) $id) : Property::active()->where('slug', (string) $id)->first();
        abort_if(! $property, 404);

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

