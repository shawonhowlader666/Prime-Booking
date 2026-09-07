<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\TenantSubscription;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SubscriptionService
{
    /**
     * Get SaaS subscription details and billing history for a hotel.
     */
    public function getSubscriptionDetails(Hotel $hotel): array
    {
        $subscriptions = TenantSubscription::where('hotel_id', $hotel->id)
            ->latest()
            ->get();

        return [
            'hotel' => $hotel,
            'plan_name' => $hotel->subscription_plan,
            'status' => $hotel->subscription_status,
            'is_active' => $hotel->isSubscriptionActive(),
            'is_locked' => (bool) $hotel->is_subscription_locked,
            'monthly_fee' => $hotel->monthly_fee,
            'expires_at' => $hotel->subscription_expires_at,
            'days_remaining' => $hotel->daysUntilExpiry(),
            'history' => $subscriptions,
        ];
    }

    /**
     * Process subscription renewal or payment for a tenant.
     */
    public function renewSubscription(Hotel $hotel, array $data): TenantSubscription
    {
        $amount = $data['amount'] ?? $hotel->monthly_fee;
        $months = (int) ($data['months'] ?? 1);
        $method = $data['payment_method'] ?? 'ONLINE_GATEWAY';

        $currentExpiry = ($hotel->subscription_expires_at && $hotel->subscription_expires_at->isFuture())
            ? $hotel->subscription_expires_at
            : now();

        $newExpiry = (clone $currentExpiry)->addMonths($months);

        $subscription = TenantSubscription::create([
            'hotel_id' => $hotel->id,
            'plan_name' => $hotel->subscription_plan,
            'amount' => $amount * $months,
            'currency' => $hotel->currency ?? 'BDT',
            'payment_method' => $method,
            'transaction_reference' => 'SUB-' . strtoupper(Str::random(10)),
            'status' => 'PAID',
            'starts_at' => $currentExpiry,
            'expires_at' => $newExpiry,
        ]);

        $hotel->subscription_status = 'ACTIVE';
        $hotel->is_subscription_locked = false;
        $hotel->subscription_expires_at = $newExpiry;
        $hotel->save();

        return $subscription;
    }
}
