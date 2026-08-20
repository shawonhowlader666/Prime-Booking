<?php

declare(strict_types=1);

namespace App\Console\Commands\Notification;

use App\Models\PriceAlert;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * CheckPriceDropAlertsCommand — Background Scheduled Worker for Price Drops.
 * Compares current hotel rates against active alerts and alerts users.
 */
class CheckPriceDropAlertsCommand extends Command
{
    protected $signature = 'alerts:check-price-drops {--dry-run : Simulate checks without updating records}';
    protected $description = 'Scan active price alerts and notify subscribers when rates drop';

    public function handle(NotificationService $notificationService): int
    {
        $this->info('Starting hotel price drop alert check...');

        $alerts = PriceAlert::active()
            ->with(['property:id,name,city,price_per_night,original_price', 'user:id,name,phone'])
            ->get();

        if ($alerts->isEmpty()) {
            $this->info('No active price alerts to process.');
            return self::SUCCESS;
        }

        $triggeredCount = 0;
        $isDryRun = (bool) $this->option('dry-run');

        foreach ($alerts as $alert) {
            $property = $alert->property;
            if (!$property) {
                continue;
            }

            $currentPrice = (float) $property->price_per_night;
            $oldPrice     = (float) $alert->current_price_at_alert;
            $targetPrice  = $alert->target_price !== null ? (float) $alert->target_price : null;

            $hasDropped = false;
            $reason = '';

            // Check if price dropped below target OR dropped below initial recorded price
            if ($targetPrice !== null && $currentPrice <= $targetPrice) {
                $hasDropped = true;
                $reason = "Price reached target ৳" . number_format($currentPrice);
            } elseif ($currentPrice < $oldPrice) {
                $dropDiff = $oldPrice - $currentPrice;
                $hasDropped = true;
                $reason = "Rate dropped by ৳" . number_format($dropDiff) . " (Now: ৳" . number_format($currentPrice) . ")";
            }

            if ($hasDropped) {
                $triggeredCount++;
                $this->line("🔔 Match found for {$alert->email}: {$property->name} — {$reason}");

                if (!$isDryRun) {
                    $alert->update([
                        'status'           => 'notified',
                        'last_notified_at' => now(),
                    ]);

                    // Send SMS alert if user has phone
                    if ($alert->user && !empty($alert->user->phone)) {
                        $smsMsg = "PRIME BOOKING: Great news! Rate for {$property->name} just dropped to ৳" . number_format($currentPrice) . "/night! Book now: " . route('hotels.show', $property->id);
                        $notificationService->sendSms($alert->user->phone, $smsMsg);
                    }

                    Log::info("Price drop alert sent to {$alert->email} for property #{$property->id} ({$property->name}).");
                }
            }
        }

        $this->info("Completed. Total active: {$alerts->count()} | Triggered notifications: {$triggeredCount}");

        return self::SUCCESS;
    }
}
