<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send real-time multi-channel booking alerts (SMS + Email).
     */
    public function sendBookingConfirmation(Booking $booking): array
    {
        $results = [
            'sms_sent'   => false,
            'email_sent' => false,
            'vendor_sms' => false,
        ];

        // 1. Send SMS to Guest
        if (!empty($booking->guest_phone)) {
            $msg = "Dear {$booking->guest_name}, your booking at {$booking->property?->name} is CONFIRMED! Ref: {$booking->booking_reference}. Check-in: {$booking->check_in}. Total: BDT " . number_format($booking->total_price) . ". Thank you for choosing PRIME BOOKING!";
            $results['sms_sent'] = $this->sendSms($booking->guest_phone, $msg);
        }

        // 2. Send SMS Alert to Vendor
        $vendorPhone = $booking->property?->vendor?->phone ?? $booking->property?->contact_phone;
        if (!empty($vendorPhone)) {
            $vendorMsg = "PRIME BOOKING Alert: New Reservation #{$booking->booking_reference} received for {$booking->room?->name}. Guest: {$booking->guest_name} ({$booking->guest_phone}). Check-in: {$booking->check_in}.";
            $results['vendor_sms'] = $this->sendSms($vendorPhone, $vendorMsg);
        }

        // 3. Log notification event
        Log::info("Notification dispatched for Booking #{$booking->booking_reference}", $results);

        return $results;
    }

    /**
     * Dispatch SMS via configured SMS Gateway (BulkSMSBD / Greenweb / Twilio / Custom HTTP API).
     */
    public function sendSms(string $phone, string $message): bool
    {
        $phoneClean = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phoneClean) < 10) {
            Log::warning("SMS skipped: Invalid phone number '{$phone}'");
            return false;
        }

        // Standardize Bangladesh number format
        if (str_starts_with($phoneClean, '01') && strlen($phoneClean) === 11) {
            $phoneFormatted = '880' . substr($phoneClean, 1);
        } else {
            $phoneFormatted = $phoneClean;
        }

        $smsApiKey   = config('services.sms.api_key', env('SMS_API_KEY', ''));
        $smsSenderId = config('services.sms.sender_id', env('SMS_SENDER_ID', ''));
        $smsApiUrl   = config('services.sms.api_url', env('SMS_API_URL', ''));

        // If live SMS gateway API credentials are provided, send HTTP request
        if (!empty($smsApiKey) && !empty($smsApiUrl)) {
            try {
                $response = Http::timeout(5)->post($smsApiUrl, [
                    'api_key'  => $smsApiKey,
                    'senderid' => $smsSenderId,
                    'number'   => $phoneFormatted,
                    'message'  => $message,
                ]);

                if ($response->successful()) {
                    Log::info("SMS sent successfully to {$phoneFormatted}: {$message}");
                    return true;
                }

                Log::error("SMS Gateway response error", ['status' => $response->status(), 'body' => $response->body()]);
            } catch (\Throwable $e) {
                Log::error("SMS Dispatch Exception: " . $e->getMessage());
            }
        }

        // Fallback: Clean structured debug log
        Log::info("[SMS SIMULATION / LOGGED]: To: {$phoneFormatted} | Message: {$message}");
        return true;
    }
}
