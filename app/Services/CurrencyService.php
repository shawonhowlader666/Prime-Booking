<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\CurrencyHelper;

/**
 * CurrencyService — Enterprise Multi-Currency Conversion & Formatting Engine.
 */
class CurrencyService
{
    public static function format(float|int|string $amountInBdt, ?string $currencyCode = null): string
    {
        return CurrencyHelper::format((float)$amountInBdt, $currencyCode);
    }

    public static function convert(float|int|string $amountInBdt, ?string $targetCurrency = null): float
    {
        return CurrencyHelper::convert((float)$amountInBdt, $targetCurrency);
    }

    public static function currentCurrency(): string
    {
        return CurrencyHelper::current();
    }

    public static function currentCode(): string
    {
        return CurrencyHelper::current();
    }

    public static function getActiveCurrency(): array
    {
        $code = CurrencyHelper::current();
        $all = CurrencyHelper::getCurrencies();
        return $all[$code] ?? $all['BDT'];
    }

    public static function getCurrencies(): array
    {
        return CurrencyHelper::getCurrencies();
    }
}

