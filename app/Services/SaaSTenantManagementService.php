<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\RatePlan;
use App\Models\RoomType;
use App\Models\TenantSubscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SaaSTenantManagementService
{
    /**
     * Get high-level SaaS ecosystem metrics across all tenants.
     */
    public function getTenantMetrics(): array
    {
        $totalHotels = Hotel::count();
        $activeSubCount = Hotel::where('subscription_status', 'ACTIVE')->where('is_subscription_locked', false)->count();
        $trialCount = Hotel::where('subscription_status', 'TRIAL')->count();
        $suspendedCount = Hotel::where(function ($q) {
            $q->where('subscription_status', 'SUSPENDED')
              ->orWhere('is_subscription_locked', true);
        })->count();

        $monthlyMrr = Hotel::where('subscription_status', 'ACTIVE')
            ->where('is_subscription_locked', false)
            ->sum('monthly_fee');

        return [
            'total_hotels' => $totalHotels,
            'active_subscriptions' => $activeSubCount,
            'free_trials' => $trialCount,
            'suspended_hotels' => $suspendedCount,
            'monthly_mrr' => (float) $monthlyMrr,
        ];
    }

    /**
     * Get paginated tenant list with search, status filtering, and eager loading.
     */
    public function getTenants(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Hotel::query()
            ->with(['users' => fn($q) => $q->where('role', 'HOTEL_OWNER')->latest()])
            ->withCount(['rooms', 'reservations']);

        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', $search)
                  ->orWhere('code', 'LIKE', $search)
                  ->orWhere('email', 'LIKE', $search)
                  ->orWhere('city', 'LIKE', $search);
            });
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'LOCKED') {
                $query->where('is_subscription_locked', true);
            } else {
                $query->where('subscription_status', $filters['status']);
            }
        }

        if (!empty($filters['plan'])) {
            $query->where('subscription_plan', $filters['plan']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Provision a brand-new Hotel tenant, its Owner account, default room types, and initial SaaS subscription.
     */
    public function createTenant(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $slug = Str::slug($data['name']) . '-' . Str::lower(Str::random(4));
            $code = !empty($data['code']) ? strtoupper($data['code']) : 'HTL-' . strtoupper(Str::random(6));

            $plan = $data['subscription_plan'] ?? 'STANDARD';
            $isTrial = ($data['is_trial'] ?? false) || ($data['subscription_status'] ?? '') === 'TRIAL';
            $status = $isTrial ? 'TRIAL' : ($data['subscription_status'] ?? 'ACTIVE');
            $trialDays = (int) ($data['trial_days'] ?? 14);
            $expiresAt = $isTrial ? now()->addDays($trialDays) : now()->addMonths((int) ($data['months'] ?? 1));

            $monthlyFee = isset($data['monthly_fee']) ? (float) $data['monthly_fee'] : 1000.00;

            $hotel = Hotel::create([
                'name' => $data['name'],
                'slug' => $slug,
                'code' => $code,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? 'Dhaka',
                'country' => $data['country'] ?? 'Bangladesh',
                'currency' => $data['currency'] ?? 'BDT',
                'tax_percentage' => $data['tax_percentage'] ?? 10.00,
                'service_charge_percentage' => $data['service_charge_percentage'] ?? 5.00,
                'sync_api_key' => 'prime_' . Str::random(24),
                'sync_webhook_secret' => 'whsec_' . Str::random(32),
                'is_active' => true,
                'subscription_plan' => $plan,
                'subscription_status' => $status,
                'subscription_expires_at' => $expiresAt,
                'monthly_fee' => $monthlyFee,
                'prime_booking_property_id' => $data['prime_booking_property_id'] ?? null,
                'is_subscription_locked' => false,
            ]);

            // Create Hotel Owner account
            $ownerEmail = $data['owner_email'] ?? $data['email'] ?? ("owner_" . Str::lower($code) . "@primepms.com");
            $ownerPassword = $data['owner_password'] ?? 'password123';

            $user = User::create([
                'hotel_id' => $hotel->id,
                'name' => $data['owner_name'] ?? ($data['name'] . ' Administrator'),
                'email' => $ownerEmail,
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($ownerPassword),
                'role' => 'HOTEL_OWNER',
                'is_active' => true,
            ]);

            // Create Starter Room Types
            $standardType = RoomType::create([
                'hotel_id' => $hotel->id,
                'name' => 'Deluxe King Suite',
                'code' => 'DLX-K',
                'base_price' => 3500.00,
                'max_occupancy' => 2,
            ]);

            RatePlan::create([
                'hotel_id' => $hotel->id,
                'room_type_id' => $standardType->id,
                'title' => 'Standard Flexible Rate',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addYears(2)->toDateString(),
                'price_per_night' => 3500.00,
                'is_active' => true,
            ]);

            // Create Initial Subscription Record
            TenantSubscription::create([
                'hotel_id' => $hotel->id,
                'plan_name' => $plan,
                'amount' => $isTrial ? 0.00 : $monthlyFee,
                'currency' => $hotel->currency,
                'payment_method' => $isTrial ? 'FREE_TRIAL' : 'SUPERADMIN_PROVISIONED',
                'transaction_reference' => 'INIT-' . strtoupper(Str::random(8)),
                'status' => 'PAID',
                'starts_at' => now(),
                'expires_at' => $expiresAt,
            ]);

            return [
                'hotel' => $hotel,
                'owner' => $user,
                'initial_password' => $ownerPassword,
            ];
        });
    }

    /**
     * Update an existing Hotel tenant's details, pricing, branding, and subscription.
     */
    public function updateTenant(Hotel $hotel, array $data): Hotel
    {
        $hotel->update([
            'name' => $data['name'] ?? $hotel->name,
            'email' => $data['email'] ?? $hotel->email,
            'phone' => $data['phone'] ?? $hotel->phone,
            'address' => $data['address'] ?? $hotel->address,
            'city' => $data['city'] ?? $hotel->city,
            'currency' => $data['currency'] ?? $hotel->currency,
            'subscription_plan' => $data['subscription_plan'] ?? $hotel->subscription_plan,
            'subscription_status' => $data['subscription_status'] ?? $hotel->subscription_status,
            'monthly_fee' => isset($data['monthly_fee']) ? (float) $data['monthly_fee'] : $hotel->monthly_fee,
            'prime_booking_property_id' => $data['prime_booking_property_id'] ?? $hotel->prime_booking_property_id,
        ]);

        if (!empty($data['subscription_expires_at'])) {
            $hotel->subscription_expires_at = Carbon::parse($data['subscription_expires_at']);
            $hotel->save();
        }

        if (isset($data['is_subscription_locked'])) {
            $hotel->is_subscription_locked = (bool) $data['is_subscription_locked'];
            $hotel->save();
        }

        return $hotel;
    }

    /**
     * Toggle hotel tenant status (ACTIVATE, SUSPEND, LOCK, UNLOCK).
     */
    public function toggleStatus(Hotel $hotel, string $action): Hotel
    {
        switch (strtoupper($action)) {
            case 'ACTIVATE':
                $hotel->subscription_status = 'ACTIVE';
                $hotel->is_subscription_locked = false;
                $hotel->is_active = true;
                if (!$hotel->subscription_expires_at || $hotel->subscription_expires_at->isPast()) {
                    $hotel->subscription_expires_at = now()->addDays(30);
                }
                break;

            case 'SUSPEND':
                $hotel->subscription_status = 'SUSPENDED';
                $hotel->is_subscription_locked = true;
                break;

            case 'LOCK':
                $hotel->is_subscription_locked = true;
                break;

            case 'UNLOCK':
                $hotel->is_subscription_locked = false;
                break;
        }

        $hotel->save();
        return $hotel;
    }

    /**
     * Extend hotel tenant subscription / free trial by a given number of days.
     */
    public function extendSubscription(Hotel $hotel, int $days = 30): TenantSubscription
    {
        $currentExpiry = ($hotel->subscription_expires_at && $hotel->subscription_expires_at->isFuture())
            ? $hotel->subscription_expires_at
            : now();

        $newExpiry = (clone $currentExpiry)->addDays($days);

        $sub = TenantSubscription::create([
            'hotel_id' => $hotel->id,
            'plan_name' => $hotel->subscription_plan,
            'amount' => $hotel->monthly_fee,
            'currency' => $hotel->currency ?? 'BDT',
            'payment_method' => 'SUPERADMIN_EXTENSION',
            'transaction_reference' => 'EXT-' . strtoupper(Str::random(8)),
            'status' => 'PAID',
            'starts_at' => $currentExpiry,
            'expires_at' => $newExpiry,
        ]);

        $hotel->subscription_status = 'ACTIVE';
        $hotel->is_subscription_locked = false;
        $hotel->subscription_expires_at = $newExpiry;
        $hotel->save();

        return $sub;
    }

    /**
     * Delete an entire Hotel tenant with safe cascade.
     */
    public function deleteTenant(Hotel $hotel): bool
    {
        return DB::transaction(function () use ($hotel) {
            // Delete associated entities
            $hotel->users()->delete();
            $hotel->rooms()->delete();
            $hotel->roomTypes()->delete();
            $hotel->reservations()->delete();
            $hotel->folios()->delete();
            $hotel->payments()->delete();
            $hotel->subscriptions()->delete();
            return (bool) $hotel->delete();
        });
    }
}
