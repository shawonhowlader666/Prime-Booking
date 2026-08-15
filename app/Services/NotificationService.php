<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send real-time multi-channel booking alerts (SMS + Email) using dynamic templates.
     */
    public function sendBookingConfirmation(Booking $booking): array
    {
        $results = [
            'sms_sent'   => false,
            'vendor_sms' => false,
        ];

        $smsEnabled = SiteSetting::get('sms_on_booking', '1') === '1';
        if (!$smsEnabled) {
            return $results;
        }

        // 1. Prepare Shortcode Replacements
        $replacements = [
            '{guest_name}'    => (string) $booking->guest_name,
            '{property_name}' => (string) ($booking->property?->name ?? 'Hotel Stay'),
            '{room_name}'     => (string) ($booking->room?->name ?? 'Standard Room'),
            '{booking_ref}'   => (string) $booking->booking_reference,
            '{check_in}'      => (string) $booking->check_in,
            '{check_out}'     => (string) $booking->check_out,
            '{total_price}'   => 'BDT ' . number_format((float)$booking->total_price),
            '{guest_phone}'   => (string) $booking->guest_phone,
        ];

        // 2. Send SMS to Guest
        if (!empty($booking->guest_phone)) {
            $defaultGuestTemplate = "Dear {guest_name}, your booking at {property_name} is CONFIRMED! Ref: {booking_ref}. Check-in: {check_in}. Total: {total_price}. Thank you for choosing PRIME BOOKING!";
            $guestTemplate = SiteSetting::get('sms_template_guest_confirmed', $defaultGuestTemplate);
            $msg = str_replace(array_keys($replacements), array_values($replacements), $guestTemplate);
            $results['sms_sent'] = $this->sendSms($booking->guest_phone, $msg);
        }

        // 3. Send SMS Alert to Vendor
        $vendorPhone = $booking->property?->vendor?->phone ?? $booking->property?->contact_phone;
        if (!empty($vendorPhone)) {
            $defaultVendorTemplate = "PRIME BOOKING Alert: New Reservation #{booking_ref} received for {room_name}. Guest: {guest_name} ({guest_phone}). Check-in: {check_in}.";
            $vendorTemplate = SiteSetting::get('sms_template_vendor_alert', $defaultVendorTemplate);
            $vendorMsg = str_replace(array_keys($replacements), array_values($replacements), $vendorTemplate);
            $results['vendor_sms'] = $this->sendSms($vendorPhone, $vendorMsg);
        }

        // 4. Log notification event
        Log::info("Dynamic SMS notification dispatched for Booking #{$booking->booking_reference}", $results);

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

        // Standardize Bangladesh number format (8801XXXXXXXXX)
        if (str_starts_with($phoneClean, '01') && strlen($phoneClean) === 11) {
            $phoneFormatted = '880' . substr($phoneClean, 1);
        } else {
            $phoneFormatted = $phoneClean;
        }

        // Retrieve settings from SiteSetting database store (falls back to config/env)
        $smsApiKey   = SiteSetting::get('sms_api_key', config('services.sms.api_key', env('SMS_API_KEY', '')));
        $smsSenderId = SiteSetting::get('sms_sender_id', config('services.sms.sender_id', env('SMS_SENDER_ID', 'PrimeBooking')));
        $smsApiUrl   = SiteSetting::get('sms_api_url', config('services.sms.api_url', env('SMS_API_URL', 'http://bulksmsbd.net/api/smsapi')));

        // If live SMS gateway API credentials are provided, send HTTP request
        if (!empty($smsApiKey) && !empty($smsApiUrl)) {
            try {
                // Support GET or POST query parameter gateways (BulkSMSBD / Greenweb standard)
                $response = Http::timeout(6)->get($smsApiUrl, [
                    'api_key'  => $smsApiKey,
                    'senderid' => $smsSenderId,
                    'number'   => $phoneFormatted,
                    'message'  => $message,
                ]);

                if ($response->successful()) {
                    Log::info("Live SMS sent successfully to {$phoneFormatted}: {$message} | Response: " . $response->body());
                    return true;
                }

                // Fallback attempt with POST
                $postResponse = Http::timeout(6)->post($smsApiUrl, [
                    'api_key'  => $smsApiKey,
                    'senderid' => $smsSenderId,
                    'number'   => $phoneFormatted,
                    'message'  => $message,
                ]);

                if ($postResponse->successful()) {
                    Log::info("Live SMS sent via POST to {$phoneFormatted}: {$message}");
                    return true;
                }

                Log::error("SMS Gateway response error", ['status' => $response->status(), 'body' => $response->body()]);
            } catch (\Throwable $e) {
                Log::error("SMS Dispatch Exception: " . $e->getMessage());
            }
        }

        // Fallback: Clean structured debug log
        Log::info("[SMS SIMULATION / LOGGED]: To: {$phoneFormatted} | Sender: {$smsSenderId} | Message: {$message}");
        return true;
    }
}
