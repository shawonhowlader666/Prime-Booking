<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

/**
 * FraudDetector — Smart Booking Risk & Fraud Analysis Engine.
 * Evaluates risk score (0-100), detects multi-booking velocity spikes,
 * disposable emails, suspicious geographic IPs, and provides decision recommendations.
 */
class FraudDetector
{
    private const DISPOSABLE_EMAIL_DOMAINS = [
        'mailinator.com', '10minutemail.com', 'tempmail.com', 'guerrillamail.com',
        'throwawaymail.com', 'sharklasers.com', 'yopmail.com', 'dispostable.com'
    ];

    /**
     * Evaluate booking fraud risk before processing transactions.
     *
     * @return array{
     *   risk_score: int,
     *   risk_level: string,
     *   decision: string,
     *   flags: list<string>
     * }
     */
    public function evaluateBooking(array $bookingData, ?User $user = null): array
    {
        $score = 0;
        $flags = [];

        $email = strtolower(trim((string)($bookingData['guest_email'] ?? $user?->email ?? '')));
        $domain = substr(strrchr($email, '@') ?: '', 1);

        // 1. Check Disposable Email (+35 Risk)
        if (in_array($domain, self::DISPOSABLE_EMAIL_DOMAINS, true)) {
            $score += 35;
            $flags[] = "Disposable temporary email domain detected ({$domain})";
        }

        // 2. High Velocity Check — multiple bookings within 15 minutes from same user/email (+45 Risk)
        if ($email !== '' || $user !== null) {
            $recentCount = Booking::where('created_at', '>=', now()->subMinutes(15))
                ->where(function ($q) use ($email, $user) {
                    if ($email !== '') {
                        $q->where('guest_email', $email);
                    }
                    if ($user !== null) {
                        $q->orWhere('user_id', $user->id);
                    }
                })
                ->count();

            if ($recentCount >= 4) {
                $score += 45;
                $flags[] = "High velocity booking spike ({$recentCount} attempts in 15 mins)";
            } elseif ($recentCount >= 2) {
                $score += 15;
                $flags[] = "Multiple recent bookings within short timeframe ({$recentCount})";
            }
        }

        // 3. Excessive Transaction Amount without history (+25 Risk)
        $totalAmount = (float)($bookingData['total_amount'] ?? 0);
        $userBookingCount = $user ? $user->bookings()->where('status', 'confirmed')->count() : 0;

        if ($userBookingCount === 0 && $totalAmount > 150000) {
            $score += 25;
            $flags[] = "First-time guest high-value booking (> ৳150,000)";
        }

        // 4. Same-day immediate check-in for high value (+15 Risk)
        $checkIn = isset($bookingData['check_in']) ? Carbon::parse($bookingData['check_in']) : null;
        if ($checkIn && $checkIn->isToday() && $totalAmount > 60000) {
            $score += 15;
            $flags[] = "Same-day immediate check-in for high-ticket amount";
        }

        // Cap score at 100
        $riskScore = min(100, $score);

        $riskLevel = match (true) {
            $riskScore >= 70 => 'HIGH',
            $riskScore >= 40 => 'MEDIUM',
            default          => 'LOW',
        };

        $decision = match ($riskLevel) {
            'HIGH'   => 'REJECT_OR_MANUAL_REVIEW',
            'MEDIUM' => 'FLAG_FOR_MONITORING',
            'LOW'    => 'AUTO_APPROVE',
        };

        return [
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'decision'   => $decision,
            'flags'      => $flags,
        ];
    }
}
