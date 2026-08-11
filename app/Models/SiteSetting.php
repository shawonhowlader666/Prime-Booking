<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * SiteSetting — Key-value store for all platform configuration.
 *
 * Usage:
 *   SiteSetting::get('vip_silver_threshold', 2)
 *   SiteSetting::set('vip_silver_threshold', 3)
 *   SiteSetting::group('vip')  → all VIP settings
 */
class SiteSetting extends Model
{
    protected $fillable = ['key', 'group', 'value', 'type', 'label', 'description', 'is_public'];

    protected $casts = ['is_public' => 'boolean'];

    private static array $cache = [];

    // ─── Static Helpers ───────────────────────────────────────────────────

    public static function get(string $key, mixed $default = null): mixed
    {
        // In-memory cache within request
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        // Redis cache — 30 min
        $value = Cache::remember("site_setting:{$key}", 1800, function () use ($key) {
            return static::where('key', $key)->value('value');
        });

        if ($value === null) return $default;

        // Cast to proper type
        $setting = static::where('key', $key)->first();
        $result  = match ($setting?->type ?? 'text') {
            'number'  => (float) $value,
            'boolean' => (bool)  (int) $value,
            'json'    => json_decode($value, true),
            default   => $value,
        };

        self::$cache[$key] = $result;
        return $result;
    }

    public static function set(string $key, mixed $value): void
    {
        $setting = static::where('key', $key)->first();
        if ($setting) {
            $setting->update(['value' => is_array($value) ? json_encode($value) : (string) $value]);
        } else {
            static::create(['key' => $key, 'value' => is_array($value) ? json_encode($value) : (string) $value, 'label' => ucwords(str_replace('_', ' ', $key))]);
        }
        // Invalidate cache
        Cache::forget("site_setting:{$key}");
        unset(self::$cache[$key]);
    }

    public static function group(string $group): \Illuminate\Support\Collection
    {
        return static::where('group', $group)->orderBy('key')->get();
    }

    // ─── VIP Helpers ──────────────────────────────────────────────────────

    public static function vipTierForBookings(int $bookingCount): string
    {
        return match(true) {
            $bookingCount >= static::get('vip_diamond_threshold', 15) => 'Diamond',
            $bookingCount >= static::get('vip_platinum_threshold', 10) => 'Platinum',
            $bookingCount >= static::get('vip_gold_threshold', 5)     => 'Gold',
            $bookingCount >= static::get('vip_silver_threshold', 2)   => 'Silver',
            default => 'Bronze',
        };
    }
}
