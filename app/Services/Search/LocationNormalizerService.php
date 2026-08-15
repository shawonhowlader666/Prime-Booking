<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Models\Location;
use Illuminate\Support\Facades\Cache;

/**
 * LocationNormalizerService — Enterprise Grade Location Alias & Synonyms Engine
 *
 * Handles:
 *  1. Multi-language queries (Bengali & English transliteration)
 *  2. Punctuation & spelling tolerance (e.g. "coxsbazar" = "Cox's Bazar", "st martin" = "Saint Martin")
 *  3. Landmark to Destination resolution (e.g. "Kolatoli" -> Cox's Bazar)
 *  4. Fast O(1) in-memory lookup with cached DB sync
 */
class LocationNormalizerService
{
    /**
     * Common synonyms and alternative spellings map
     */
    private const ALIASES = [
        // Cox's Bazar
        'coxsbazar'     => "Cox's Bazar",
        'coxs bazar'    => "Cox's Bazar",
        'cox bazar'     => "Cox's Bazar",
        'coxes bazar'   => "Cox's Bazar",
        'কক্সবাজার'      => "Cox's Bazar",
        'কক্স বাজার'     => "Cox's Bazar",
        'kolatoli'      => "Cox's Bazar",
        'inani'         => "Cox's Bazar",
        'laboni'        => "Cox's Bazar",
        'marine drive'  => "Cox's Bazar",

        // Chittagong
        'chattogram'    => "Chittagong",
        'chittagong'    => "Chittagong",
        'ctg'           => "Chittagong",
        'চট্টগ্রাম'       => "Chittagong",
        'patenga'       => "Chittagong",
        'agrabad'       => "Chittagong",

        // Sylhet & Sreemangal
        'sylhet'        => "Sylhet",
        'সিলেট'          => "Sylhet",
        'sreemangal'    => "Sylhet",
        'srimangal'     => "Sylhet",
        'শ্রীমঙ্গল'       => "Sylhet",
        'jaflong'       => "Sylhet",
        'জাফলং'          => "Sylhet",
        'tanguar'       => "Sunamganj",
        'টাঙ্গুয়ার হাওর' => "Sunamganj",

        // Sundarbans
        'sundarban'     => "Sundarbans",
        'sundarbans'    => "Sundarbans",
        'shundarban'    => "Sundarbans",
        'সুন্দরবন'        => "Sundarbans",
        'mongla'        => "Sundarbans",
        'মোংলা'          => "Sundarbans",

        // Sajek & Rangamati
        'sajek'         => "Sajek",
        'sajek valley'  => "Sajek",
        'সাজেক'          => "Sajek",
        'rangamati'     => "Sajek",
        'রাঙ্গামাটি'     => "Sajek",
        'ruilui'        => "Sajek",

        // Kuakata
        'kuakata'       => "Kuakata",
        'কুয়াকাটা'       => "Kuakata",

        // Bandarban
        'bandarban'     => "Bandarban",
        'বান্দরবান'      => "Bandarban",
        'nilgiri'       => "Bandarban",
        'নীলগিরি'        => "Bandarban",

        // Saint Martin
        'saint martin'  => "Saint Martin",
        'st martin'     => "Saint Martin",
        'st. martin'    => "Saint Martin",
        'stmartins'     => "Saint Martin",
        'সেন্টমার্টিন'    => "Saint Martin",

        // Dhaka
        'dhaka'         => "Dhaka",
        'dacca'         => "Dhaka",
        'ঢাকা'           => "Dhaka",
        'uttara'        => "Dhaka",
        'gulshan'       => "Dhaka",
        'banani'        => "Dhaka",
        'dhanmondi'     => "Dhaka",
        'mirpur'        => "Dhaka",
    ];

    /**
     * Normalize a raw search term into canonical destination and coordinates.
     *
     * @param string $term
     * @return array{normalized: string, canonical: ?string, latitude: ?float, longitude: ?float}
     */
    public function resolve(string $term): array
    {
        $clean = trim($term);
        if ($clean === '') {
            return ['normalized' => '', 'canonical' => null, 'latitude' => null, 'longitude' => null];
        }

        $lower = mb_strtolower($clean, 'UTF-8');

        // 1. Check direct alias table
        $canonical = self::ALIASES[$lower] ?? null;

        // 2. If not matched, try fuzzy substring match against alias keys
        if (!$canonical) {
            foreach (self::ALIASES as $key => $target) {
                if (str_contains($lower, $key) || str_contains($key, $lower)) {
                    $canonical = $target;
                    break;
                }
            }
        }

        $lookupName = $canonical ?: $clean;

        // 3. Retrieve location coordinates from database cache
        $location = Cache::remember("loc_geo:" . md5($lookupName), 3600, function () use ($lookupName) {
            return Location::where('name', 'like', "%{$lookupName}%")
                ->orWhere('city', 'like', "%{$lookupName}%")
                ->first();
        });

        return [
            'normalized' => $clean,
            'canonical'  => $canonical ?: ($location?->city ?? $clean),
            'latitude'   => $location?->latitude ? (float) $location->latitude : null,
            'longitude'  => $location?->longitude ? (float) $location->longitude : null,
        ];
    }
}
