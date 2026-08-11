<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Review;
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

        // Related properties (same city)
        $related = $this->repository->getRelated($property, 4);

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
            'reviews',
            'checkIn',
            'checkOut',
            'guests',
            'nights',
        ));
    }
}
