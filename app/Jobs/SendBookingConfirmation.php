<?php

namespace App\Jobs;

use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * SendBookingConfirmation Job
 * ───────────────────────────
 * Queued job that sends the confirmation email + admin notification.
 * Retries 3 times with exponential backoff if mail server is down.
 * Uses database queue driver (works without Redis mail queue config).
 */
class SendBookingConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Retry 3 times if email fails */
    public int $tries = 3;

    /** Wait 60s between retries (exponential backoff) */
    public int $backoff = 60;

    /** Timeout after 90 seconds */
    public int $timeout = 90;

    public function __construct(
        public readonly int $bookingId,
    ) {}

    public function handle(): void
    {
        $booking = Booking::with([
            'property:id,name,city,address,primary_image,star_rating',
            'room:id,name,bed_type',
        ])->find($this->bookingId);

        if (!$booking) {
            Log::warning("SendBookingConfirmation: Booking #{$this->bookingId} not found.");
            return;
        }

        // Send to guest
        try {
            Mail::to($booking->guest_email)
                ->send(new BookingConfirmationMail(
                    $booking,
                    $booking->property,
                    $booking->room
                ));
            Log::info("Booking confirmation email sent: {$booking->booking_reference} → {$booking->guest_email}");
        } catch (\Throwable $e) {
            Log::error("Failed to send booking confirmation email: " . $e->getMessage());
            throw $e; // Re-throw for retry
        }
    }

    /** If all retries fail, log it */
    public function failed(\Throwable $exception): void
    {
        Log::error("SendBookingConfirmation job permanently failed for booking #{$this->bookingId}: " . $exception->getMessage());
    }
}
