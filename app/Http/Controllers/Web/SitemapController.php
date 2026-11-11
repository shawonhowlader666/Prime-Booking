<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * Dynamic XML Sitemap Generator for Googlebot & Bingbot.
 * Generates valid sitemap index and URL entries for destinations, hotels, and core services.
 */
class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = Cache::remember('sitemap_xml_output', 21600, function () {
            $baseUrl = rtrim(config('app.url', 'https://primebooking.com.bd'), '/');
            if (!str_starts_with($baseUrl, 'http')) {
                $baseUrl = 'https://primebooking.com.bd';
            }

            $now = now()->toIso8601String();

            // Core High Priority Pages
            $urls = [
                [
                    'loc' => "{$baseUrl}/",
                    'lastmod' => $now,
                    'changefreq' => 'daily',
                    'priority' => '1.0',
                ],
                [
                    'loc' => "{$baseUrl}/hotels",
                    'lastmod' => $now,
                    'changefreq' => 'daily',
                    'priority' => '0.9',
                ],
                [
                    'loc' => "{$baseUrl}/deals",
                    'lastmod' => $now,
                    'changefreq' => 'daily',
                    'priority' => '0.8',
                ],
                [
                    'loc' => "{$baseUrl}/services",
                    'lastmod' => $now,
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ],
                [
                    'loc' => "{$baseUrl}/flights",
                    'lastmod' => $now,
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ],
                [
                    'loc' => "{$baseUrl}/tours",
                    'lastmod' => $now,
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ],
                [
                    'loc' => "{$baseUrl}/transfers",
                    'lastmod' => $now,
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ],
                [
                    'loc' => "{$baseUrl}/blog",
                    'lastmod' => $now,
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ],
            ];

            // High Volume Destination Pages (Agoda & GoZayaan Search Ranking Top Landing Pages)
            // High Volume Destination Pages (Full Bangladesh Nationwide Coverage)
            $topDestinations = [
                // Chittagong Division & Tourist Belts
                'coxs-bazar'   => '1.0',
                'kolatoli'     => '0.8',
                'inani'        => '0.8',
                'himchori'     => '0.8',
                'saint-martin' => '0.8',
                'teknaf'       => '0.7',
                'chittagong'   => '0.9',
                'patenga'      => '0.7',
                'sajek'        => '0.9',
                'bandarban'    => '0.8',
                'rangamati'    => '0.8',
                'khagrachhari' => '0.8',
                'cumilla'      => '0.7',
                'comilla'      => '0.7',
                'feni'         => '0.7',
                'noakhali'     => '0.7',
                'chandpur'     => '0.7',
                'brahmanbaria' => '0.7',

                // Dhaka Division & Suburbs
                'dhaka'        => '1.0',
                'gulshan'      => '0.9',
                'banani'       => '0.8',
                'uttara'       => '0.8',
                'dhanmondi'    => '0.8',
                'motijheel'    => '0.7',
                'mirpur'       => '0.7',
                'gazipur'      => '0.8',
                'savar'        => '0.7',
                'narayanganj'  => '0.7',
                'tangail'      => '0.7',
                'narsingdi'    => '0.7',
                'manikganj'    => '0.7',
                'munshiganj'   => '0.7',
                'faridpur'     => '0.7',
                'gopalganj'    => '0.7',

                // Sylhet Division (Tea Gardens & Haors)
                'sylhet'       => '0.9',
                'sreemangal'   => '0.9',
                'jaflong'      => '0.8',
                'tanguar-haor' => '0.9',
                'sunamganj'    => '0.8',
                'moulvibazar'  => '0.8',
                'habiganj'     => '0.7',

                // Barishal Division (Sea Beach & Riverine)
                'kuakata'      => '0.9',
                'barishal'     => '0.8',
                'patuakhali'   => '0.7',
                'bhola'        => '0.7',
                'barguna'      => '0.7',
                'pirojpur'     => '0.7',
                'jhalokathi'   => '0.7',

                // Khulna Division & Sundarbans
                'sundarban'    => '0.9',
                'sundarbans'   => '0.9',
                'khulna'       => '0.8',
                'mongla'       => '0.8',
                'jessore'      => '0.8',
                'bagerhat'     => '0.7',
                'satkhira'     => '0.7',
                'kushtia'      => '0.7',
                'chuadanga'    => '0.7',
                'meherpur'     => '0.7',
                'jhenaidah'    => '0.7',
                'narail'       => '0.7',
                'magura'       => '0.7',

                // Rajshahi Division (Heritage & Silk)
                'rajshahi'     => '0.8',
                'bogra'        => '0.8',
                'bogura'       => '0.8',
                'pabna'        => '0.7',
                'natore'       => '0.7',
                'naogaon'      => '0.7',
                'sirajganj'    => '0.7',
                'chapainawabganj' => '0.7',
                'joypurhat'    => '0.7',

                // Rangpur Division (North Bengal)
                'rangpur'      => '0.8',
                'dinajpur'     => '0.8',
                'panchagarh'   => '0.8',
                'tetulia'      => '0.8',
                'saidpur'      => '0.7',
                'kurigram'     => '0.7',
                'lalmonirhat'  => '0.7',
                'nilphamari'   => '0.7',
                'gaibandha'    => '0.7',
                'thakurgaon'   => '0.7',

                // Mymensingh Division
                'mymensingh'   => '0.8',
                'netrokona'    => '0.7',
                'kishoreganj'  => '0.7',
                'jamalpur'     => '0.7',
                'sherpur'      => '0.7',
            ];

            foreach ($topDestinations as $slug => $prio) {
                $urls[] = [
                    'loc' => "{$baseUrl}/hotels-in-{$slug}",
                    'lastmod' => $now,
                    'changefreq' => 'daily',
                    'priority' => $prio,
                ];
                // High search volume intents for top tourist hubs
                if (in_array($slug, ['coxs-bazar', 'sylhet', 'sajek', 'kuakata', 'sreemangal', 'dhaka', 'chittagong'])) {
                    $urls[] = [
                        'loc' => "{$baseUrl}/resorts-in-{$slug}",
                        'lastmod' => $now,
                        'changefreq' => 'daily',
                        'priority' => '0.8',
                    ];
                    $urls[] = [
                        'loc' => "{$baseUrl}/cheap-hotels-in-{$slug}",
                        'lastmod' => $now,
                        'changefreq' => 'daily',
                        'priority' => '0.8',
                    ];
                    $urls[] = [
                        'loc' => "{$baseUrl}/5-star-hotels-in-{$slug}",
                        'lastmod' => $now,
                        'changefreq' => 'daily',
                        'priority' => '0.8',
                    ];
                }
            }

            // All Active Hotels & Resorts
            try {
                $properties = Property::select(['id', 'slug', 'updated_at'])
                    ->active()
                    ->orderByDesc('updated_at')
                    ->limit(1000)
                    ->get();

                foreach ($properties as $prop) {
                    $propUrl = !empty($prop->slug) 
                        ? "{$baseUrl}/hotel/{$prop->slug}" 
                        : "{$baseUrl}/hotels/{$prop->id}";

                    $urls[] = [
                        'loc' => $propUrl,
                        'lastmod' => $prop->updated_at ? $prop->updated_at->toIso8601String() : $now,
                        'changefreq' => 'weekly',
                        'priority' => '0.8',
                    ];
                }
            } catch (\Throwable $e) {
                // Fallback gracefully
            }

            // Assemble XML string
            $xmlLines = [
                '<?xml version="1.0" encoding="UTF-8"?>',
                '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">',
            ];

            foreach ($urls as $u) {
                $xmlLines[] = '  <url>';
                $xmlLines[] = '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1, 'UTF-8') . '</loc>';
                $xmlLines[] = '    <lastmod>' . $u['lastmod'] . '</lastmod>';
                $xmlLines[] = '    <changefreq>' . $u['changefreq'] . '</changefreq>';
                $xmlLines[] = '    <priority>' . $u['priority'] . '</priority>';
                $xmlLines[] = '  </url>';
            }

            $xmlLines[] = '</urlset>';

            return implode("\n", $xmlLines);
        });

        return response($xml, 200, [
            'Content-Type' => 'text/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=21600, s-maxage=86400',
        ]);
    }
}
