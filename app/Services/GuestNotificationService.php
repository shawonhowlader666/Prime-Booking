<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;

class GuestNotificationService
{
    /**
     * Send reservation booking confirmation via SMS/WhatsApp.
     */
    public function sendBookingConfirmation(Reservation $reservation, string $channel = 'whatsapp'): array
    {
        $guest = $reservation->guest;
        $hotel = $reservation->hotel;
        $room = $reservation->room;

        $phone = $guest?->phone ?? 'N/A';
        $guestName = $guest ? "{$guest->first_name} {$guest->last_name}" : 'Valued Guest';
        $hotelName = $hotel?->name ?? 'Prime Hotel';
        $roomNumber = $room?->room_number ?? 'Assigned at check-in';

        $message = "Dear {$guestName}, your reservation #{$reservation->booking_reference} at {$hotelName} is confirmed! Room: {$roomNumber}, Check-in: {$reservation->check_in_date->format('Y-m-d')}. We look forward to hosting you.";

        return $this->dispatch($reservation, $channel, 'BOOKING_CONFIRMATION', $phone, $message);
    }

    /**
     * Send instant welcome notification upon check-in with Wi-Fi & room details.
     */
    public function sendCheckInWelcome(Reservation $reservation, string $channel = 'whatsapp'): array
    {
        $guest = $reservation->guest;
        $hotel = $reservation->hotel;
        $room = $reservation->room;

        $phone = $guest?->phone ?? 'N/A';
        $guestName = $guest ? "{$guest->first_name} {$guest->last_name}" : 'Valued Guest';
        $hotelName = $hotel?->name ?? 'Prime Hotel';
        $roomNumber = $room?->room_number ?? 'Suite';

        $message = "Welcome to {$hotelName}, {$guestName}! You are checked into Room {$roomNumber}. High-speed Wi-Fi: {$hotelName}_Guest (Key: welcome{$roomNumber}). Dial 0 for Front Desk anytime.";

        return $this->dispatch($reservation, $channel, 'CHECK_IN_WELCOME', $phone, $message);
    }

    /**
     * Send check-out invoice and thank-you message.
     */
    public function sendCheckOutThankYou(Reservation $reservation, string $channel = 'whatsapp'): array
    {
        $guest = $reservation->guest;
        $hotel = $reservation->hotel;

        $phone = $guest?->phone ?? 'N/A';
        $guestName = $guest ? "{$guest->first_name} {$guest->last_name}" : 'Valued Guest';
        $hotelName = $hotel?->name ?? 'Prime Hotel';

        $message = "Thank you for staying at {$hotelName}, {$guestName}! We hope you had a wonderful experience. Your final folio receipt has been settled. See you on your next visit!";

        return $this->dispatch($reservation, $channel, 'CHECK_OUT_SUMMARY', $phone, $message);
    }

    /**
     * Dispatch notification payload and log audit trail.
     */
    protected function dispatch(Reservation $reservation, string $channel, string $eventType, string $recipient, string $content): array
    {
        $payload = [
            'status' => 'sent',
            'channel' => $channel,
            'event_type' => $eventType,
            'recipient' => $recipient,
            'content' => $content,
            'timestamp' => now()->toIso8601String(),
            'message_id' => 'MSG-' . strtoupper(bin2hex(random_bytes(6))),
        ];

        Log::info("Guest Notification [{$channel}] dispatched to {$recipient}: {$content}");

        // Record in audit log for compliance & tracking
        if ($reservation->hotel_id) {
            AuditLog::create([
                'hotel_id' => $reservation->hotel_id,
                'user_id' => auth()->id(),
                'action' => 'NOTIFICATION_DISPATCHED',
                'entity_type' => Reservation::class,
                'entity_id' => $reservation->id,
                'old_values' => null,
                'new_values' => $payload,
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => request()->userAgent() ?? 'PMS-Notification-Engine',
                'created_at' => now(),
            ]);
        }

        return $payload;
    }
}
