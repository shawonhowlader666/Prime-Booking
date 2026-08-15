<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'name'                    => $this->name,
            'slug'                    => $this->slug,
            'type'                    => $this->type,
            'city'                    => $this->city,
            'star_rating'             => $this->star_rating,
            'rating_score'            => (float)$this->rating_score,
            'sub_scores'              => $this->sub_scores,
            'ai_highlights'           => $this->ai_highlights,
            'total_reviews'           => $this->total_reviews,
            'address'                 => $this->address,
            'description'             => $this->description,
            'price_per_night'         => (float)$this->price_per_night,
            'original_price'          => (float)$this->original_price,
            'primary_image'           => $this->primary_image,
            'video_url'               => $this->video_url,
            'images'                  => (array)($this->images ?? []),
            'amenities'               => (array)($this->amenities ?? []),
            'is_featured'             => (bool)$this->is_featured,
            'free_cancellation'       => (bool)$this->free_cancellation,
            'no_credit_card_required' => (bool)$this->no_credit_card_required,
            'latitude'                => $this->latitude,
            'longitude'               => $this->longitude,
            'nearest_landmark'        => $this->nearest_landmark,
            'nearby_landmarks'        => $this->nearby_landmarks_list,
            'checkin_time'            => $this->checkin_time ?? '14:00',
            'checkout_time'           => $this->checkout_time ?? '12:00',
            'total_floors'            => $this->total_floors,
            'total_rooms_count'       => $this->total_rooms_count,
            'year_built'              => $this->year_built,
            'languages_spoken'        => (array)($this->languages_spoken ?? []),
            'pets_policy'             => $this->pets_policy ?? 'Pets Not Allowed',
            'rooms'                   => RoomResource::collection($this->whenLoaded('rooms')),
        ];
    }
}
