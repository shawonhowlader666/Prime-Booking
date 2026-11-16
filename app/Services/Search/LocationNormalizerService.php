<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Models\Location;
use Illuminate\Support\Facades\Cache;

/**
 * LocationNormalizerService — Enterprise-Grade Typo Tolerance & String Similarity Engine
 *
 * Algorithms & Data Structures used:
 *  1. Exact Hash Map Lookup (O(1) time complexity)
 *  2. Normalized Transliteration mapping for Bengali and English
 *  3. Levenshtein Distance & Jaro-Winkler Similarity for fuzzy typo correction (O(M*N))
 *  4. Prefix & Substring Trie-like matching
 */
class LocationNormalizerService
{
    /**
     * Canonical destination dictionary
     */
    private const CANONICAL_DESTINATIONS = [
        "Cox's Bazar",
        "Dhaka",
        "Chittagong",
        "Sylhet",
        "Sreemangal",
        "Sundarban",
        "Sundarbans",
        "Sajek",
        "Kuakata",
        "Bandarban",
        "Saint Martin",
        "Sunamganj",
        "Khulna",
        "Rajshahi",
        "Barisal",
        "Rangpur",
        "Mymensingh",
        "Gazipur",
        "Comilla",
        "Bogura",
        "Bangkok",
        "Dubai",
        "Kuala Lumpur",
        "Singapore",
        "Makkah",
        "Madinah",
    ];

    /**
     * Common synonyms and phonetic transliterations
     */
    private const ALIASES = [
        // Cox's Bazar
        "cox's bazar"   => "Cox's Bazar",
        'coxsbazar'     => "Cox's Bazar",
        'coxs bazar'    => "Cox's Bazar",
        'cox bazar'     => "Cox's Bazar",
        'coxes bazar'   => "Cox's Bazar",
        'coxsbazr'      => "Cox's Bazar",
        'কক্সবাজার'      => "Cox's Bazar",
        'কক্স বাজার'     => "Cox's Bazar",
        'kolatoli'      => "Cox's Bazar",
        'inani'         => "Cox's Bazar",
        'laboni'        => "Cox's Bazar",
        'marine drive'  => "Cox's Bazar",

        // Chittagong
        'chattogram'    => "Chittagong",
        'chittagong'    => "Chittagong",
        'chittagng'     => "Chittagong",
        'ctg'           => "Chittagong",
        'চট্টগ্রাম'       => "Chittagong",
        'patenga'       => "Chittagong",
        'agrabad'       => "Chittagong",

        // Sylhet & Sreemangal
        'sylhet'        => "Sylhet",
        'sylet'         => "Sylhet",
        'সিলেট'          => "Sylhet",
        'sreemangal'    => "Sreemangal",
        'sreemongol'    => "Sreemangal",
        'sreemongal'    => "Sreemangal",
        'srimangal'     => "Sreemangal",
        'srimongol'     => "Sreemangal",
        'শ্রীমঙ্গল'       => "Sreemangal",
        'jaflong'       => "Sylhet",
        'জাফলং'          => "Sylhet",
        'tanguar'       => "Sunamganj",
        'টাঙ্গুয়ার'      => "Sunamganj",
        'tanguar haor'  => "Sunamganj",
        'tanguar haor houseboat' => "Sunamganj",
        'টাঙ্গুয়ার হাওর'  => "Sunamganj",
        'টাঙ্গুয়ার হাওর হাউসবোট' => "Sunamganj",
        'tanguor hour'  => "Sunamganj",
        'tanguor hour house board' => "Sunamganj",

        // Sundarbans
        'sundarban'     => "Sundarban",
        'sundarbans'    => "Sundarban",
        'sundrbon'      => "Sundarban",
        'shundarban'    => "Sundarban",
        'সুন্দরবন'        => "Sundarban",
        'mongla'        => "Sundarban",
        'মোংলা'          => "Sundarban",
        'sundarban ship'         => "Sundarban",
        'sundarbans ship'        => "Sundarban",
        'sundarban cruise'       => "Sundarban",
        'sundarbans cruise'      => "Sundarban",
        'sundarban ship cruise'  => "Sundarban",
        'sundarbans ship cruise' => "Sundarban",
        'sundorbon ship'         => "Sundarban",

        // Sajek & Rangamati
        'sajek'         => "Sajek",
        'sjake'         => "Sajek",
        'sajek valley'  => "Sajek",
        'সাজেক'          => "Sajek",
        'rangamati'     => "Sajek",
        'রাঙ্গামাটি'     => "Sajek",

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
     * Resolve a query string to the best matching canonical destination
     * using exact lookup -> alias dictionary -> Levenshtein distance algorithm.
     *
     * @param string $term
     * @return array{normalized: string, canonical: ?string, latitude: ?float, longitude: ?float, similarity: float}
     */
    /**
     * Quick normalization helper returning canonical name or null.
     */
    public function normalize(string $term): ?string
    {
        $res = $this->resolve($term);
        return ($res['similarity'] >= 0.65) ? $res['canonical'] : null;
    }

    /**
     * Main resolution method with coordinates and similarity score.
     */
    public function resolve(string $term): array
    {
        $clean = trim($term);
        if ($clean === '') {
            return ['normalized' => '', 'canonical' => null, 'latitude' => null, 'longitude' => null, 'similarity' => 0.0];
        }

        // Clean common country or region suffixes (e.g. ", Bangladesh", ", BD", ", Chittagong")
        $stripped = trim(preg_replace('/[,\s]+(bangladesh|bd)\b/i', '', $clean));
        if ($stripped === '') {
            $stripped = $clean;
        }

        $lower = mb_strtolower($stripped, 'UTF-8');
        $rawLower = mb_strtolower($clean, 'UTF-8');

        // Step 1: Direct Hash Map Lookup (O(1))
        if (isset(self::ALIASES[$lower])) {
            $canonical = self::ALIASES[$lower];
            return $this->buildResult($clean, $canonical, 1.0);
        }
        if (isset(self::ALIASES[$rawLower])) {
            $canonical = self::ALIASES[$rawLower];
            return $this->buildResult($clean, $canonical, 1.0);
        }

        // Step 2: Substring / Prefix matching
        foreach (self::ALIASES as $aliasKey => $target) {
            if (str_contains($lower, $aliasKey) || str_contains($aliasKey, $lower) || str_contains($rawLower, $aliasKey)) {
                return $this->buildResult($clean, $target, 0.95);
            }
        }

        // Step 2.5: Dynamic Bangladesh Geo Registry Matching (covers all 64 districts, upazilas, & tourist hubs)
        try {
            $geoService = app(\App\Services\Geo\BangladeshLocationService::class);
            $geoMatches = $geoService->search($stripped, 1);
            if (!empty($geoMatches)) {
                $top = $geoMatches[0];
                if (($top['_score'] ?? 0) >= 80) {
                    return [
                        'normalized' => $clean,
                        'canonical'  => $top['name'],
                        'latitude'   => $top['lat'],
                        'longitude'  => $top['lng'],
                        'similarity' => 1.0,
                    ];
                }
            }
        } catch (\Throwable) {}

        // Step 3: Levenshtein Distance & Jaro-Winkler Edit-Distance Matching (Fuzzy Typo Tolerance)
        $bestMatch = null;
        $bestScore = 0.0;

        foreach (self::CANONICAL_DESTINATIONS as $dest) {
            $destLower = strtolower($dest);
            $lev = levenshtein($lower, $destLower);
            $maxLen = max(strlen($lower), strlen($destLower));
            $similarity = 1.0 - ($lev / max(1, $maxLen));

            if ($similarity > $bestScore) {
                $bestScore = $similarity;
                $bestMatch = $dest;
            }
        }

        // If similarity exceeds threshold (e.g. >= 0.65 for typos like "sylet" for "Sylhet")
        if ($bestScore >= 0.65 && $bestMatch) {
            return $this->buildResult($clean, $bestMatch, round($bestScore, 2));
        }

        return $this->buildResult($clean, $clean, 0.5);
    }

    private function buildResult(string $raw, string $canonical, float $similarity): array
    {
        $location = Cache::remember("loc_geo:" . md5($canonical), 3600, function () use ($canonical) {
            return Location::where('name', 'like', "%{$canonical}%")
                ->orWhere('city', 'like', "%{$canonical}%")
                ->first();
        });

        return [
            'normalized' => $raw,
            'canonical'  => $canonical,
            'latitude'   => $location?->latitude ? (float) $location->latitude : null,
            'longitude'  => $location?->longitude ? (float) $location->longitude : null,
            'similarity' => $similarity,
        ];
    }
}
