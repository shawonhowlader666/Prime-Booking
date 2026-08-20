<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CompareController — Side-by-Side Property Comparison Matrix.
 * Allows travelers to compare up to 4 hotels side-by-side on price, ratings,
 * room specs, amenities, policies, and location.
 */
class CompareController extends Controller
{
    public function index(Request $request): View
    {
        $rawIds = $request->input('ids', []);
        if (is_string($rawIds)) {
            $ids = array_filter(array_map('intval', explode(',', $rawIds)));
        } else {
            $ids = array_filter(array_map('intval', (array)$rawIds));
        }

        // Limit to max 4 properties for side-by-side readability
        $ids = array_slice(array_unique($ids), 0, 4);

        $properties = collect();
        if (!empty($ids)) {
            $properties = Property::active()
                ->whereIn('id', $ids)
                ->with(['rooms:id,property_id,name,price_per_night,max_adults,bed_type,room_size_sqm'])
                ->get();
        }

        // If fewer than 2 properties provided, add top-rated suggestions
        if ($properties->count() < 2) {
            $needed = 3 - $properties->count();
            $suggestions = Property::active()
                ->whereNotIn('id', $properties->pluck('id'))
                ->orderByDesc('rating_score')
                ->limit($needed)
                ->get();
            $properties = $properties->merge($suggestions);
        }

        // Common comparison feature list
        $features = [
            'price_per_night' => 'Price / Night',
            'star_rating'     => 'Star Rating',
            'rating_score'    => 'Guest Score',
            'total_reviews'   => 'Total Reviews',
            'city'            => 'Destination',
            'address'         => 'Address & Area',
            'free_cancel'     => 'Free Cancellation',
            'no_credit_card'  => 'No Credit Card Needed',
            'rooms_count'     => 'Available Room Types',
        ];

        $standardAmenities = [
            'Free Wi-Fi', 'Swimming Pool', 'Air conditioning', 'Free parking',
            'Fitness center', 'Restaurant', 'Airport transfer', 'Spa'
        ];

        return view('pages.compare', compact('properties', 'features', 'standardAmenities'));
    }
}
