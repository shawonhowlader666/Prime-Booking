<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;

class CurrencyHelper
{
    private const RATES = [
        'BDT' => ['name' => 'Bangladeshi Taka', 'symbol' => 'BDT ', 'rate' => 1.0,    'decimals' => 0],
        'USD' => ['name' => 'US Dollar',       'symbol' => '$',    'rate' => 0.0083, 'decimals' => 2],
        'EUR' => ['name' => 'Euro',            'symbol' => '€',    'rate' => 0.0077, 'decimals' => 2],
        'GBP' => ['name' => 'British Pound',   'symbol' => '£',    'rate' => 0.0066, 'decimals' => 2],
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

        return $config['symbol'] . number_format($converted, $decimals);
    }

    public static function getCurrencies(): array
    {
        return self::RATES;
    }
}
