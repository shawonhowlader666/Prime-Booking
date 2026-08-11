<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * BookingConfirmationMail
 * ───────────────────────
 * Queued email sent to the guest after a successful booking.
 * Uses queue() instead of send() so it never blocks the HTTP response.
 */
class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Booking  $booking,
        public readonly Property $property,
        public readonly ?Room    $room = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Booking Confirmed — ' . $this->booking->booking_reference . ' | Prime Aviation',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
            with: [
                'booking'  => $this->booking,
                'property' => $this->property,
                'room'     => $this->room,
            ],
        );
    }
}
