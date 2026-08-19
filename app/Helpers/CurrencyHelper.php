<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;

class CurrencyHelper
{
    private const RATES = [
        'BDT' => ['name' => 'Bangladeshi Taka',   'symbol' => 'BDT ৳', 'prefix' => '৳',   'rate' => 1.0,     'decimals' => 0],
        'USD' => ['name' => 'US Dollar',          'symbol' => 'USD $', 'prefix' => '$',   'rate' => 0.0083,  'decimals' => 2],
        'EUR' => ['name' => 'Euro',               'symbol' => 'EUR €', 'prefix' => '€',   'rate' => 0.0077,  'decimals' => 2],
        'GBP' => ['name' => 'British Pound',      'symbol' => 'GBP £', 'prefix' => '£',   'rate' => 0.0066,  'decimals' => 2],
        'SGD' => ['name' => 'Singapore Dollar',   'symbol' => 'SGD S$', 'prefix' => 'S$', 'rate' => 0.011,   'decimals' => 2],
        'MYR' => ['name' => 'Malaysian Ringgit',  'symbol' => 'MYR RM', 'prefix' => 'RM', 'rate' => 0.039,   'decimals' => 2],
        'THB' => ['name' => 'Thai Baht',          'symbol' => 'THB ฿', 'prefix' => '฿',   'rate' => 0.30,    'decimals' => 2],
        'INR' => ['name' => 'Indian Rupee',       'symbol' => 'INR ₹', 'prefix' => '₹',   'rate' => 0.70,    'decimals' => 2],
        'AED' => ['name' => 'Emirati Dirham',     'symbol' => 'AED د.إ', 'prefix' => 'AED', 'rate' => 0.031, 'decimals' => 2],
        'SAR' => ['name' => 'Saudi Riyal',        'symbol' => 'SAR ﷼', 'prefix' => 'SAR', 'rate' => 0.031,  'decimals' => 2],
        'CAD' => ['name' => 'Canadian Dollar',    'symbol' => 'CAD C$', 'prefix' => 'C$', 'rate' => 0.011,   'decimals' => 2],
        'AUD' => ['name' => 'Australian Dollar',  'symbol' => 'AUD A$', 'prefix' => 'A$', 'rate' => 0.013,   'decimals' => 2],
        'CHF' => ['name' => 'Swiss Franc',        'symbol' => 'CHF CHF', 'prefix' => 'CHF', 'rate' => 0.0075, 'decimals' => 2],
        'CNY' => ['name' => 'Chinese Yuan',       'symbol' => 'RMB ¥', 'prefix' => '¥',   'rate' => 0.060,   'decimals' => 2],
        'JPY' => ['name' => 'Japanese Yen',       'symbol' => 'JPY ¥', 'prefix' => '¥',   'rate' => 1.25,    'decimals' => 0],
    ];

    public static function current(): string
    {
        return (string) Session::get('app_currency', 'BDT');
    }

    public static function setCurrency(string $code): void
    {
        $code = strtoupper(trim($code));
        if (array_key_exists($code, self::RATES)) {
            Session::put('app_currency', $code);
        }
    }

    public static function convert(float $amountInBdt, ?string $targetCurrency = null): float
    {
        $curr = $targetCurrency ?: static::current();
        $rate = self::RATES[$curr]['rate'] ?? 1.0;
        return round($amountInBdt * $rate, 2);
    }

    public static function format(float $amountInBdt, ?string $targetCurrency = null): string
    {
        $curr = $targetCurrency ?: static::current();
        $config = self::RATES[$curr] ?? self::RATES['BDT'];
        $converted = static::convert($amountInBdt, $curr);
        $decimals = $config['decimals'];

        if ($curr === 'BDT') {
            return 'BDT ' . number_format($converted, 0);
        }

        return $config['symbol'] . ' ' . number_format($converted, $decimals);
    }

    public static function getCurrencies(): array
    {
        return self::RATES;
    }
}
