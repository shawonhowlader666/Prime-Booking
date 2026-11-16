<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Services\Geo\BangladeshLocationService;
use Illuminate\Database\Seeder;

class BangladeshLocationSeeder extends Seeder
{
    public function run(): void
    {
        $geoService = app(BangladeshLocationService::class);
        $data = $geoService->loadData();

        echo "Seeding Bangladesh Geo Locations into database...\n";

        // 1. Seed Tourist Hotspots
        $countSpots = 0;
        foreach ($data['tourist_spots'] ?? [] as $spot) {
            Location::updateOrCreate(
                ['name' => $spot['name']],
                [
                    'city'         => $spot['name'],
                    'country'      => 'Bangladesh',
                    'country_code' => 'BD',
                    'latitude'     => $spot['lat'],
                    'longitude'    => $spot['lng'],
                    'is_popular'   => true,
                ]
            );
            $countSpots++;
        }
        echo "  -> Seeded {$countSpots} Tourist Hotspots.\n";

        // 2. Seed All 64 Districts
        $countDist = 0;
        foreach ($data['districts'] ?? [] as $d) {
            Location::updateOrCreate(
                ['name' => $d['name']],
                [
                    'city'         => $d['name'],
                    'country'      => 'Bangladesh',
                    'country_code' => 'BD',
                    'latitude'     => $d['lat'],
                    'longitude'    => $d['lng'],
                    'is_popular'   => (bool) ($d['is_popular'] ?? false),
                ]
            );
            $countDist++;
        }
        echo "  -> Seeded {$countDist} Official Districts.\n";

        // 3. Seed International Hubs
        $countIntl = 0;
        foreach ($data['international'] ?? [] as $intl) {
            Location::updateOrCreate(
                ['name' => $intl['name']],
                [
                    'city'         => $intl['name'],
                    'country'      => $intl['country'],
                    'country_code' => $intl['country_code'],
                    'latitude'     => $intl['lat'],
                    'longitude'    => $intl['lng'],
                    'is_popular'   => true,
                ]
            );
            $countIntl++;
        }
        echo "  -> Seeded {$countIntl} International Hubs.\n";

        echo "Total active locations in database: " . Location::count() . "\n";
    }
}
