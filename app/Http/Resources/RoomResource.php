<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'property_id'        => $this->property_id,
            'name'               => $this->name,
            'bed_type'           => $this->bed_type,
            'max_adults'         => $this->max_adults ?? 2,
            'max_children'       => $this->max_children ?? 1,
            'price_per_night'    => (float)$this->price_per_night,
            'room_size_sqm'      => $this->room_size_sqm,
            'formatted_size'     => $this->formatted_size,
            'view_type'          => $this->view_type ?? 'City View',
            'bathroom_count'     => $this->bathroom_count ?? 1,
            'bathroom_features'  => (array)($this->bathroom_features ?? []),
            'smoking_policy'     => $this->smoking_policy ?? 'Non-Smoking',
            'balcony_type'       => $this->balcony_type ?? 'Private Balcony',
            'extra_bed_allowed'  => (bool)$this->extra_bed_allowed,
            'breakfast_included' => (bool)$this->breakfast_included,
            'free_cancellation'  => (bool)$this->free_cancellation,
            'primary_image'      => $this->images[0] ?? null,
            'images'             => (array)($this->images ?? []),
        ];
    }
}
