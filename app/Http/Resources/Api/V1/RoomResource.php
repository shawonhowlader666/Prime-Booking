<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hotel_id' => $this->hotel_id,
            'room_number' => $this->room_number,
            'floor' => $this->floor,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'room_type' => [
                'id' => $this->roomType?->id,
                'name' => $this->roomType?->name,
                'code' => $this->roomType?->code,
                'base_price' => $this->roomType?->base_price,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
