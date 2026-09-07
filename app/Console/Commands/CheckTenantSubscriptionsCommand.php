<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Hotel;
use App\Models\TenantSubscription;
use Illuminate\Console\Command;

class CheckTenantSubscriptionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:check-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit all tenant subscriptions, identify expiring licenses, and flag suspended/expired accounts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting SaaS Tenant Subscription Health & Expiry Audit...');

        $subscriptions = TenantSubscription::with('hotel')->get();
        $expiredCount = 0;
        $warningCount = 0;
        $activeCount = 0;

        foreach ($subscriptions as $subscription) {
            $hotel = $subscription->hotel;
            if (!$hotel) {
                continue;
            }

            $endDate = $subscription->expires_at ?? $subscription->subscription_ends_at ?? $subscription->trial_ends_at;

            if ($endDate && $endDate->isPast()) {
                // Subscription has elapsed
                if ($subscription->status !== 'expired' && $subscription->status !== 'suspended') {
                    $subscription->status = 'expired';
                    $subscription->save();

                    $expiredCount++;
                    $this->warn("Marked tenant subscription EXPIRED for: {$hotel->name} [{$hotel->code}]");

                    AuditLog::create([
                        'hotel_id' => $hotel->id,
                        'user_id' => null,
                        'action' => 'SUBSCRIPTION_EXPIRED_CRON',
                        'entity_type' => TenantSubscription::class,
                        'entity_id' => $subscription->id,
                        'old_values' => ['status' => 'active'],
                        'new_values' => ['status' => 'expired'],
                        'ip_address' => '127.0.0.1',
                        'user_agent' => 'CLI-Cron-Scheduler',
                        'created_at' => now(),
                    ]);
                }
            } elseif ($endDate && $endDate->diffInDays(now()) <= 7) {
                // Expiring within 7 days
                $warningCount++;
                $this->line("Tenant expiring soon ({$endDate->diffForHumans()}): {$hotel->name} [{$hotel->code}]");
            } else {
                $activeCount++;
            }
        }

        $this->info("Subscription audit completed. Active: {$activeCount}, Expiring Soon: {$warningCount}, Newly Expired: {$expiredCount}");

        return self::SUCCESS;
    }
}
