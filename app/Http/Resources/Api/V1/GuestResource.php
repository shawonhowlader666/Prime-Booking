<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hotel_id' => $this->hotel_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'id_type' => $this->id_type,
            'is_vip' => $this->is_vip,
            'notes' => $this->notes,
            'reservations_count' => $this->whenCounted('reservations'),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
