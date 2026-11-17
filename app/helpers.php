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

if (!function_exists('per_page')) {
    /**
     * Rows per page chosen in a list's "per page" selector, limited to the offered sizes.
     */
    function per_page(int $default = 15): int
    {
        $requested = (int) request('per_page', $default);

        return in_array($requested, [10, 15, 20, 25, 50, 100], true) ? $requested : $default;
    }
}
