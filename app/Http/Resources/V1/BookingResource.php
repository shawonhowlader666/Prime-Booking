<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'reference'         => $this->booking_reference,
            'status'            => $this->effective_status,
            'payment_status'    => $this->payment_status,
            'payment_method'    => $this->payment_method,

            // Guest
            'guest'  => [
                'name'  => $this->guest_name,
                'email' => $this->guest_email,
                'phone' => $this->guest_phone,
            ],

            // Dates
            'check_in'  => $this->check_in?->toDateString(),
            'check_out' => $this->check_out?->toDateString(),
            'nights'    => $this->nights_count,
            'guests'    => (int)($this->guests ?? $this->adults ?? 2),

            // Pricing
            'pricing' => [
                'per_night'  => (float)($this->price_per_night ?? 0),
                'subtotal'   => (float)($this->subtotal ?? $this->total_amount ?? 0),
                'tax'        => (float)($this->tax_amount ?? 0),
                'total'      => (float)($this->amount),
                'currency'   => 'BDT',
            ],

            // Nested relationships (when loaded)
            'property'  => $this->whenLoaded('property', fn() => [
                'id'    => $this->property->id,
                'name'  => $this->property->name,
                'city'  => $this->property->city,
                'image' => $this->property->primary_image,
            ]),
            'room' => $this->whenLoaded('room', fn() => $this->room ? [
                'id'   => $this->room->id,
                'name' => $this->room->name,
            ] : null),

            'special_requests' => $this->special_requests,
            'created_at'       => $this->created_at?->toISOString(),
        ];
    }
}
