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

if (!function_exists('media_url')) {
    /**
     * Resolve media path into CDN / Cloudflare R2 / AWS S3 / Local URL.
     * Guarantees zero disk choking and infinite scalability.
     */
    function media_url(?string $path, ?string $fallback = null): string
    {
        return \App\Services\Media\MediaService::url($path, $fallback);
    }
}
