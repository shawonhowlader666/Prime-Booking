<?php

declare(strict_types=1);

namespace App\Services\Geo;

use Illuminate\Support\Facades\Cache;

/**
 * BangladeshLocationService — Authoritative Bangladesh Geo & Tourism Registry
 *
 * Provides instant search, categorization, and coordinates across:
 *  - 8 Administrative Divisions
 *  - 64 Official Districts (100% complete)
 *  - 280+ Upazilas & Thanas
 *  - Top 25+ Tourist Hotspots & Resort Hubs (Cox's Bazar, Sajek, Saint Martin, etc.)
 *  - Popular International Hubs (Bangkok, Dubai, Singapore, Makkah, etc.)
 */
class BangladeshLocationService
{
    private const CACHE_KEY = 'bd_geo_registry_v1';
    private const TTL = 86400; // 24 hours

    private static ?array $data = null;

    /**
     * Load the raw JSON geo dataset.
     */
    public function loadData(): array
    {
        if (self::$data !== null) {
            return self::$data;
        }

        self::$data = Cache::remember(self::CACHE_KEY, self::TTL, function () {
            $path = resource_path('data/bangladesh_geo.json');
            if (!file_exists($path)) {
                return [];
            }
            $json = file_get_contents($path);
            return json_decode($json, true) ?: [];
        });

        return self::$data;
    }

    /**
     * Search across all Bangladesh districts, upazilas, tourist hotspots, and international hubs.
     * Supports both English and Bengali queries with fuzzy scoring.
     *
     * @return list<array{name: string, bn_name: string, type: string, district: ?string, division: ?string, country: string, country_code: string, lat: float, lng: float, is_popular: bool, display_name: string, _score: int}>
     */
    public function search(string $query, int $limit = 10): array
    {
        $q = trim(strtolower($query));
        if ($q === '') {
            return $this->getPopularDestinations($limit);
        }

        $data = $this->loadData();
        $matches = [];
        $seen = [];

        // 1. Search Tourist Hotspots (Highest priority)
        foreach ($data['tourist_spots'] ?? [] as $spot) {
            $nameEn = strtolower($spot['name']);
            $nameBn = strtolower($spot['bn_name'] ?? '');
            $dist   = strtolower($spot['district'] ?? '');

            $score = 0;
            if ($nameEn === $q || $nameBn === $q) {
                $score = 150;
            } elseif (str_starts_with($nameEn, $q) || str_starts_with($nameBn, $q)) {
                $score = 120;
            } elseif (str_contains($nameEn, $q) || str_contains($nameBn, $q) || str_contains($dist, $q)) {
                $score = 90;
            }

            if ($score > 0) {
                $key = 'spot_' . $spot['name'];
                $seen[$key] = true;
                $matches[] = [
                    'name'         => $spot['name'],
                    'bn_name'      => $spot['bn_name'],
                    'type'         => 'Tourist Spot',
                    'district'     => $spot['district'],
                    'division'     => $spot['division'],
                    'country'      => 'Bangladesh',
                    'country_code' => 'BD',
                    'lat'          => (float) $spot['lat'],
                    'lng'          => (float) $spot['lng'],
                    'is_popular'   => true,
                    'display_name' => "{$spot['name']}, {$spot['district']} (Bangladesh)",
                    '_score'       => $score,
                ];
            }
        }

        // 2. Search 64 Districts
        foreach ($data['districts'] ?? [] as $d) {
            $nameEn = strtolower($d['name']);
            $nameBn = strtolower($d['bn_name'] ?? '');
            $div    = strtolower($d['division'] ?? '');

            $score = 0;
            if ($nameEn === $q || $nameBn === $q) {
                $score = 140;
            } elseif (str_starts_with($nameEn, $q) || str_starts_with($nameBn, $q)) {
                $score = 110;
            } elseif (str_contains($nameEn, $q) || str_contains($nameBn, $q) || str_contains($div, $q)) {
                $score = 80;
            }

            if ($score > 0) {
                $key = 'dist_' . $d['name'];
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    if (!empty($d['is_popular'])) {
                        $score += 15;
                    }
                    $matches[] = [
                        'name'         => $d['name'],
                        'bn_name'      => $d['bn_name'],
                        'type'         => 'District',
                        'district'     => $d['name'],
                        'division'     => $d['division'],
                        'country'      => 'Bangladesh',
                        'country_code' => 'BD',
                        'lat'          => (float) $d['lat'],
                        'lng'          => (float) $d['lng'],
                        'is_popular'   => (bool) ($d['is_popular'] ?? false),
                        'display_name' => "{$d['name']} District, {$d['division']} (Bangladesh)",
                        '_score'       => $score,
                    ];
                }
            }
        }

        // 3. Search Upazilas / Thanas
        foreach ($data['upazilas'] ?? [] as $u) {
            $nameEn = strtolower($u['name']);
            $nameBn = strtolower($u['bn_name'] ?? '');
            $dist   = strtolower($u['district'] ?? '');

            $score = 0;
            if ($nameEn === $q || $nameBn === $q) {
                $score = 120;
            } elseif (str_starts_with($nameEn, $q) || str_starts_with($nameBn, $q)) {
                $score = 95;
            } elseif (str_contains($nameEn, $q) || str_contains($nameBn, $q) || str_contains($dist, $q)) {
                $score = 65;
            }

            if ($score > 0) {
                $key = 'upz_' . $u['name'] . '_' . $u['district'];
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    // Find district coordinates as baseline
                    $distInfo = $this->findDistrictByName($u['district']);
                    $lat = $distInfo ? (float) $distInfo['lat'] : 23.8103;
                    $lng = $distInfo ? (float) $distInfo['lng'] : 90.4125;

                    $matches[] = [
                        'name'         => $u['name'],
                        'bn_name'      => $u['bn_name'],
                        'type'         => 'Upazila / Area',
                        'district'     => $u['district'],
                        'division'     => $u['division'],
                        'country'      => 'Bangladesh',
                        'country_code' => 'BD',
                        'lat'          => $lat,
                        'lng'          => $lng,
                        'is_popular'   => false,
                        'display_name' => "{$u['name']}, {$u['district']}, {$u['division']}",
                        '_score'       => $score,
                    ];
                }
            }
        }

        // 4. Search International Destinations
        foreach ($data['international'] ?? [] as $intl) {
            $nameEn = strtolower($intl['name']);
            $nameBn = strtolower($intl['bn_name'] ?? '');
            $cntry  = strtolower($intl['country'] ?? '');

            $score = 0;
            if ($nameEn === $q || $nameBn === $q) {
                $score = 130;
            } elseif (str_starts_with($nameEn, $q) || str_starts_with($nameBn, $q)) {
                $score = 100;
            } elseif (str_contains($nameEn, $q) || str_contains($nameBn, $q) || str_contains($cntry, $q)) {
                $score = 70;
            }

            if ($score > 0) {
                $key = 'intl_' . $intl['name'];
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $matches[] = [
                        'name'         => $intl['name'],
                        'bn_name'      => $intl['bn_name'],
                        'type'         => 'International',
                        'district'     => null,
                        'division'     => null,
                        'country'      => $intl['country'],
                        'country_code' => $intl['country_code'],
                        'lat'          => (float) $intl['lat'],
                        'lng'          => (float) $intl['lng'],
                        'is_popular'   => true,
                        'display_name' => "{$intl['name']}, {$intl['country']}",
                        '_score'       => $score,
                    ];
                }
            }
        }

        // Sort descending by calculated score
        usort($matches, fn ($a, $b) => $b['_score'] <=> $a['_score']);

        return array_slice($matches, 0, $limit);
    }

    /**
     * Get all 64 districts.
     */
    public function getAllDistricts(): array
    {
        $data = $this->loadData();
        return $data['districts'] ?? [];
    }

    /**
     * Get top popular travel destinations.
     */
    public function getPopularDestinations(int $limit = 10): array
    {
        $data = $this->loadData();
        $spots = $data['tourist_spots'] ?? [];

        $list = [];
        foreach (array_slice($spots, 0, $limit) as $spot) {
            $list[] = [
                'name'         => $spot['name'],
                'bn_name'      => $spot['bn_name'],
                'type'         => 'Tourist Spot',
                'district'     => $spot['district'],
                'division'     => $spot['division'],
                'country'      => 'Bangladesh',
                'country_code' => 'BD',
                'lat'          => (float) $spot['lat'],
                'lng'          => (float) $spot['lng'],
                'is_popular'   => true,
                'display_name' => "{$spot['name']}, {$spot['district']}",
                '_score'       => 100,
            ];
        }

        return $list;
    }

    /**
     * Get grouped list for dropdowns (Top Tourist Hubs, All 64 Districts, Major Upazilas).
     */
    public function getGroupedDropdownOptions(): array
    {
        $data = $this->loadData();

        $groups = [
            'Top Destinations' => [],
            'All 64 Districts' => [],
            'Popular Areas & Upazilas' => [],
            'International' => [],
        ];

        foreach ($data['tourist_spots'] ?? [] as $s) {
            $groups['Top Destinations'][] = [
                'value' => $s['name'],
                'label' => "🌟 {$s['name']} ({$s['bn_name']})",
                'lat'   => $s['lat'],
                'lng'   => $s['lng'],
            ];
        }

        foreach ($data['districts'] ?? [] as $d) {
            $groups['All 64 Districts'][] = [
                'value' => $d['name'],
                'label' => "📍 {$d['name']} ({$d['bn_name']}) - {$d['division']}",
                'lat'   => $d['lat'],
                'lng'   => $d['lng'],
            ];
        }

        foreach (array_slice($data['upazilas'] ?? [], 0, 80) as $u) {
            $groups['Popular Areas & Upazilas'][] = [
                'value' => $u['name'],
                'label' => "🏘️ {$u['name']} ({$u['bn_name']}), {$u['district']}",
                'lat'   => null,
                'lng'   => null,
            ];
        }

        foreach ($data['international'] ?? [] as $intl) {
            $groups['International'][] = [
                'value' => $intl['name'],
                'label' => "✈️ {$intl['name']}, {$intl['country']}",
                'lat'   => $intl['lat'],
                'lng'   => $intl['lng'],
            ];
        }

        return $groups;
    }

    /**
     * Find district details by name.
     */
    public function findDistrictByName(string $name): ?array
    {
        $nameLower = strtolower(trim($name));
        $data = $this->loadData();
        foreach ($data['districts'] ?? [] as $d) {
            if (strtolower($d['name']) === $nameLower || strtolower($d['bn_name'] ?? '') === $nameLower) {
                return $d;
            }
        }
        return null;
    }

    /**
     * Get a complete flat searchable list of all tourist spots, 64 districts, upazilas, and international hubs.
     * Ideal for datalists, autocomplete, and search suggestions.
     *
     * @return list<array{name: string, bn_name: string, type: string, district: ?string, division: ?string, lat: ?float, lng: ?float, display_name: string}>
     */
    public function getFlatSearchableList(): array
    {
        $data = $this->loadData();
        $list = [];
        $seen = [];

        // 1. Tourist spots first
        foreach ($data['tourist_spots'] ?? [] as $s) {
            $key = strtolower($s['name']);
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $list[] = [
                'name'         => $s['name'],
                'bn_name'      => $s['bn_name'],
                'type'         => 'Tourist Spot',
                'district'     => $s['district'],
                'division'     => $s['division'],
                'lat'          => (float) $s['lat'],
                'lng'          => (float) $s['lng'],
                'display_name' => "🌟 {$s['name']} ({$s['bn_name']}) - {$s['district']}",
            ];
        }

        // 2. All 64 Districts
        foreach ($data['districts'] ?? [] as $d) {
            $key = strtolower($d['name']);
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $list[] = [
                'name'         => $d['name'],
                'bn_name'      => $d['bn_name'],
                'type'         => 'District',
                'district'     => $d['name'],
                'division'     => $d['division'],
                'lat'          => (float) $d['lat'],
                'lng'          => (float) $d['lng'],
                'display_name' => "📍 {$d['name']} ({$d['bn_name']}) - {$d['division']}",
            ];
        }

        // 3. Upazilas / Thanas
        foreach ($data['upazilas'] ?? [] as $u) {
            $key = strtolower($u['name']);
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $distInfo = $this->findDistrictByName($u['district']);
            $list[] = [
                'name'         => $u['name'],
                'bn_name'      => $u['bn_name'],
                'type'         => 'Upazila / Area',
                'district'     => $u['district'],
                'division'     => $u['division'],
                'lat'          => $distInfo ? (float) $distInfo['lat'] : null,
                'lng'          => $distInfo ? (float) $distInfo['lng'] : null,
                'display_name' => "🏘️ {$u['name']} ({$u['bn_name']}), {$u['district']}",
            ];
        }

        // 4. International
        foreach ($data['international'] ?? [] as $intl) {
            $key = strtolower($intl['name']);
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $list[] = [
                'name'         => $intl['name'],
                'bn_name'      => $intl['bn_name'],
                'country'      => $intl['country'],
                'type'         => 'International',
                'district'     => null,
                'division'     => null,
                'lat'          => (float) $intl['lat'],
                'lng'          => (float) $intl['lng'],
                'display_name' => "✈️ {$intl['name']} ({$intl['bn_name']}), {$intl['country']}",
            ];
        }

        return $list;
    }

    /**
     * Clear registry cache.
     */
    public static function clearCache(): void
    {
        self::$data = null;
        Cache::forget(self::CACHE_KEY);
    }
}
