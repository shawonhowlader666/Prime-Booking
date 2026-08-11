<?php

use App\Services\CurrencyService;

if (!function_exists('format_price')) {
    /**
     * Convert and format price in active session currency (BDT, USD, EUR)
     */
    function format_price($amountInBdt, ?string $currencyCode = null): string
    {
        return CurrencyService::format($amountInBdt, $currencyCode);
    }
}
