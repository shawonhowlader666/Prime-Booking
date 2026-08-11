<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * PropertyResource — Standard API representation of a Property.
 * Used in all API responses to ensure consistent field naming and structure.
 */
class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'slug'            => $this->slug,
            'type'            => $this->type,
            'city'            => $this->city,
            'address'         => $this->address,

            // Ratings
            'star_rating'     => (int) $this->star_rating,
            'rating_score'    => (float) $this->rating_score,
            'total_reviews'   => (int) $this->total_reviews,

            // Pricing
            'price_per_night' => (float) $this->price_per_night,
            'original_price'  => $this->original_price ? (float) $this->original_price : null,
            'discount_pct'    => $this->discount_percent,
            'currency'        => 'BDT',

            // Media
            'primary_image'   => $this->primary_image,
            'images'          => $this->images ?? [],

            // Features
            'amenities'       => $this->amenities ?? [],
            'is_featured'     => (bool) $this->is_featured,
            'status'          => $this->status,

            // Rooms (when loaded)
            'rooms'           => RoomResource::collection($this->whenLoaded('rooms')),

            // Computed
            'stars_display'   => str_repeat('★', $this->star_rating ?? 0),
            'booking_url'     => url("/book/{$this->id}"),

            // Timestamps
            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),
        ];
    }
}
