<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'bed_type'            => $this->bed_type,
            'max_adults'          => (int)  $this->max_adults,
            'max_children'        => (int)  ($this->max_children ?? 0),
            'room_size_sqm'       => (float)($this->room_size_sqm ?? 0),
            'price_per_night'     => (float) $this->price_per_night,
            'total_rooms'         => (int)  ($this->total_rooms ?? 1),
            'breakfast_included'  => (bool) ($this->breakfast_included ?? false),
            'free_cancellation'   => (bool) ($this->free_cancellation ?? false),
            'facilities'          => $this->facilities ?? [],
        ];
    }
}
