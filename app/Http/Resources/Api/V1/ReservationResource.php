<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reservation_code' => $this->reservation_code,
            'hotel_id' => $this->hotel_id,
            'status' => $this->status,
            'source' => $this->source,
            'check_in_date' => $this->check_in_date?->toDateString(),
            'check_out_date' => $this->check_out_date?->toDateString(),
            'adults' => $this->adults,
            'children' => $this->children,
            'total_amount' => (float) $this->total_amount,
            'paid_amount' => (float) $this->paid_amount,
            'balance_amount' => (float) $this->balance_amount,
            'guest' => [
                'id' => $this->guest?->id,
                'name' => $this->guest?->full_name,
                'email' => $this->guest?->email,
                'phone' => $this->guest?->phone,
            ],
            'room' => $this->room ? [
                'id' => $this->room->id,
                'room_number' => $this->room->room_number,
                'status' => $this->room->status,
            ] : null,
            'room_type' => [
                'id' => $this->roomType?->id,
                'name' => $this->roomType?->name,
                'code' => $this->roomType?->code,
            ],
            'folio' => $this->folio ? [
                'id' => $this->folio->id,
                'folio_number' => $this->folio->folio_number,
                'total_charges' => (float) $this->folio->total_charges,
                'total_payments' => (float) $this->folio->total_payments,
                'balance' => (float) $this->folio->balance,
                'status' => $this->folio->status,
            ] : null,
            'checked_in_at' => $this->checked_in_at?->toIso8601String(),
            'checked_out_at' => $this->checked_out_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
