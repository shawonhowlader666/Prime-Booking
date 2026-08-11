<?php

namespace App\Services;

class CurrencyService
{
    protected static array $currencies = [
        'BDT' => ['symbol' => 'BDT ', 'rate' => 1.0,     'name' => 'Bangladeshi Taka', 'flag' => '🇧🇩', 'decimals' => 0],
        'USD' => ['symbol' => '$',    'rate' => 0.00833, 'name' => 'US Dollar',       'flag' => '🇺🇸', 'decimals' => 2],
        'EUR' => ['symbol' => '€',    'rate' => 0.00769, 'name' => 'Euro',            'flag' => '🇪🇺', 'decimals' => 2],
        'GBP' => ['symbol' => '£',    'rate' => 0.0066,  'name' => 'British Pound',   'flag' => '🇬🇧', 'decimals' => 2],
    ];

    public static function format($amountInBdt, ?string $currencyCode = null): string
    {
        $code = strtoupper($currencyCode ?: session('app_currency', session('currency', 'BDT')));

        if (!isset(self::$currencies[$code])) {
            $code = 'BDT';
        }

        $info = self::$currencies[$code];
        $converted = (float)$amountInBdt * $info['rate'];
        $decimals = $info['decimals'];

        if ($code === 'BDT') {
            return 'BDT ' . number_format($converted, 0);
        }

        return $info['symbol'] . number_format($converted, $decimals);
    }

    public static function getActiveCurrency(): array
    {
        $code = strtoupper(session('app_currency', session('currency', 'BDT')));
        return self::$currencies[$code] ?? self::$currencies['BDT'];
    }

    public static function getCurrencies(): array
    {
        return self::$currencies;
    }
}
