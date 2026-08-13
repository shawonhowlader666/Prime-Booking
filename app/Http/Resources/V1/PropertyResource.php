<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * PropertyResource — Standard API representation of a Property.
 * 100% Null-safe for stdClass objects & Eloquent models.
 */
class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $res = $this->resource;

        $id            = is_array($res) ? ($res['id'] ?? null) : ($res->id ?? null);
        $name          = is_array($res) ? ($res['name'] ?? '') : ($res->name ?? '');
        $slug          = is_array($res) ? ($res['slug'] ?? '') : ($res->slug ?? '');
        $type          = is_array($res) ? ($res['type'] ?? 'hotel') : ($res->type ?? 'hotel');
        $city          = is_array($res) ? ($res['city'] ?? '') : ($res->city ?? '');
        $address       = is_array($res) ? ($res['address'] ?? '') : ($res->address ?? '');
        $starRating    = is_array($res) ? ($res['star_rating'] ?? 5) : ($res->star_rating ?? 5);
        $ratingScore   = is_array($res) ? ($res['rating_score'] ?? 8.5) : ($res->rating_score ?? 8.5);
        $totalReviews  = is_array($res) ? ($res['total_reviews'] ?? 0) : ($res->total_reviews ?? 0);
        $pricePerNight = is_array($res) ? ($res['price_per_night'] ?? 0) : ($res->price_per_night ?? 0);
        $originalPrice = is_array($res) ? ($res['original_price'] ?? null) : ($res->original_price ?? null);
        $primaryImage  = is_array($res) ? ($res['primary_image'] ?? null) : ($res->primary_image ?? null);
        $images        = is_array($res) ? ($res['images'] ?? []) : ($res->images ?? []);
        $amenities     = is_array($res) ? ($res['amenities'] ?? []) : ($res->amenities ?? []);
        $isFeatured    = is_array($res) ? ($res['is_featured'] ?? false) : ($res->is_featured ?? false);
        $status        = is_array($res) ? ($res['status'] ?? 'active') : ($res->status ?? 'active');

        $discountPct = 0;
        if ($originalPrice > $pricePerNight && $originalPrice > 0) {
            $discountPct = round((($originalPrice - $pricePerNight) / $originalPrice) * 100);
        }

        return [
            'id'              => $id,
            'name'            => $name,
            'slug'            => $slug,
            'type'            => $type,
            'city'            => $city,
            'address'         => $address,

            // Ratings
            'star_rating'     => (int) $starRating,
            'rating_score'    => (float) $ratingScore,
            'total_reviews'   => (int) $totalReviews,

            // Pricing
            'price_per_night' => (float) $pricePerNight,
            'original_price'  => $originalPrice ? (float) $originalPrice : null,
            'discount_pct'    => (int) $discountPct,
            'currency'        => 'BDT',

            // Media
            'primary_image'   => $primaryImage ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
            'images'          => is_array($images) ? array_values($images) : [],

            // Features
            'amenities'       => is_array($amenities) ? array_values($amenities) : [],
            'is_featured'     => (bool) $isFeatured,
            'status'          => $status,

            // Rooms (when loaded)
            'rooms'           => RoomResource::collection($this->whenLoaded('rooms')),

            // Computed
            'stars_display'   => str_repeat('★', max(0, (int) $starRating)),
            'booking_url'     => url("/book/{$id}"),
        ];
    }
}
