<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Opens every GET page of the site as a guest, a customer, a vendor and an
 * admin against seeded data. No page may answer with a server error.
 */
class EveryPageRendersTest extends TestCase
{
    use RefreshDatabase;

    private Property $property;
    private Room $room;
    private Booking $booking;
    private array $users = [];

    /** Routes that leave the site, stream huge files, or log the user out. */
    private const SKIP = [
        'auth/{provider}/redirect', 'auth/{provider}/callback', 'admin/system/backup/download',
        '_ignition', 'up', 'storage/{path}', 'sanctum/csrf-cookie',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['customer', 'vendor', 'admin', 'super_admin'] as $role) {
            $this->users[$role] = User::create([
                'name' => ucfirst($role) . ' User', 'email' => $role . '@crawl.test', 'phone' => '0170000000' . strlen($role),
                'password' => bcrypt('Secret123!'), 'role' => $role, 'status' => 'active', 'email_verified_at' => now(),
            ]);
        }

        $this->property = Property::create([
            'vendor_id' => $this->users['vendor']->id, 'name' => 'Crawl Hotel', 'slug' => 'crawl-hotel', 'type' => 'hotel',
            'city' => 'Dhaka', 'address' => 'Gulshan', 'price_per_night' => 5000, 'original_price' => 6500, 'star_rating' => 4,
            'status' => 'active', 'description' => 'Seeded listing for the page crawler.', 'latitude' => '23.78', 'longitude' => '90.41',
            'amenities' => ['Free Wi-Fi', 'Pool'], 'primary_image' => 'https://cdn.example.com/cover.jpg',
            'images' => [['url' => 'https://cdn.example.com/a.jpg', 'category' => 'rooms', 'title' => 'A']],
        ]);

        $this->room = Room::create([
            'property_id' => $this->property->id, 'name' => 'Deluxe', 'price_per_night' => 5000, 'total_rooms' => 5,
            'max_adults' => 2, 'max_children' => 1, 'max_guests' => 3, 'status' => 'active', 'bed_type' => 'King',
            'images' => ['https://cdn.example.com/r.jpg'], 'facilities' => ['AC'],
        ]);

        $this->booking = Booking::create([
            'booking_reference' => 'CRAWL-0001', 'property_id' => $this->property->id, 'room_id' => $this->room->id,
            'user_id' => $this->users['customer']->id, 'guest_name' => 'Customer User', 'guest_email' => 'customer@crawl.test',
            'guest_phone' => '01700000001', 'check_in' => now()->subDays(4)->toDateString(), 'check_out' => now()->subDays(2)->toDateString(),
            'guests' => 2, 'rooms_count' => 1, 'nights' => 2, 'price_per_night' => 5000, 'subtotal' => 10000, 'tax_amount' => 750,
            'total_price' => 10750, 'total_amount' => 10750, 'payment_method' => 'sslcommerz', 'payment_status' => 'paid', 'status' => 'completed',
        ]);

        $this->seedOptional();
    }

    /** One row in each secondary table, where the table exists, so index and detail pages have something to render. */
    private function seedOptional(): void
    {
        $now = now();
        $vendorId = $this->users['vendor']->id;
        $customerId = $this->users['customer']->id;

        $rows = [
            'reviews' => ['property_id' => $this->property->id, 'user_id' => $customerId, 'guest_name' => 'Customer User', 'rating' => 4, 'comment' => 'Good stay', 'status' => 'approved'],
            'coupons' => ['code' => 'CRAWL10', 'type' => 'percentage', 'amount' => 10, 'min_spend' => 0, 'status' => 'active', 'vendor_id' => $vendorId],
            'inquiries' => ['name' => 'Guest', 'email' => 'g@crawl.test', 'phone' => '01700000009', 'message' => 'Hello', 'property_id' => $this->property->id, 'status' => 'pending'],
            'payouts' => ['vendor_id' => $vendorId, 'amount' => 1000, 'status' => 'pending', 'payment_method' => 'bkash'],
            'tour_packages' => ['title' => 'Crawl Tour', 'name' => 'Crawl Tour', 'slug' => 'crawl-tour', 'price' => 5000, 'status' => 'active', 'vendor_id' => $vendorId, 'duration' => '2 Days', 'destination' => 'Sylhet', 'description' => 'Tour'],
            'blog_posts' => ['title' => 'Crawl Post', 'slug' => 'crawl-post', 'content' => '<p>Body</p>', 'excerpt' => 'Body', 'is_published' => 1, 'status' => 'published', 'published_at' => $now, 'author_id' => $this->users['admin']->id],
            'support_tickets' => ['user_id' => $vendorId, 'subject' => 'Help', 'message' => 'Need help', 'status' => 'open', 'priority' => 'normal', 'ticket_number' => 'TK-1', 'category' => 'general'],
            'promotions' => ['title' => 'Promo', 'is_active' => 1, 'sort_order' => 1],
            'featured_destinations' => ['name' => 'Dhaka', 'slug' => 'dhaka', 'is_active' => 1, 'sort_order' => 1],
            'deals' => ['title' => 'Deal', 'is_active' => 1],
            'cms_contents' => ['key' => 'about', 'title' => 'About', 'content' => 'About us'],
            'airport_transfers' => ['name' => 'Sedan', 'vehicle_type' => 'sedan', 'price' => 1500, 'is_active' => 1, 'status' => 'active', 'capacity' => 3],
            'email_templates' => ['key' => 'booking_confirmed', 'name' => 'Booking Confirmed', 'subject' => 'Confirmed', 'body' => 'Hi', 'is_active' => 1],
            'hero_slides' => ['title' => 'Slide', 'image' => 'https://cdn.example.com/h.jpg', 'is_active' => 1, 'sort_order' => 1],
            'tenants' => ['name' => 'Tenant', 'slug' => 'tenant', 'status' => 'active'],
            'amenities' => ['name' => 'Free Wi-Fi', 'slug' => 'free-wifi'],
        ];

        foreach ($rows as $table => $row) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            $columns = Schema::getColumnListing($table);
            $data = array_intersect_key($row, array_flip($columns));
            foreach (['created_at', 'updated_at'] as $ts) {
                if (in_array($ts, $columns, true)) {
                    $data[$ts] = $now;
                }
            }
            try {
                DB::table($table)->insert($data);
            } catch (\Throwable $e) {
                // Optional fixture: a table with extra required columns simply stays empty.
            }
        }
    }

    private function parameterValue(string $name, string $uri): string|int
    {
        $firstId = function (string $table): int {
            return Schema::hasTable($table) ? (int) (DB::table($table)->value('id') ?? 1) : 1;
        };

        return match (true) {
            in_array($name, ['propertyId', 'property'], true) => $this->property->id,
            $name === 'roomId' => $this->room->id,
            $name === 'reference' && str_contains($uri, 'package') => 'NONE',
            $name === 'reference' && str_contains($uri, 'transfer') => 'NONE',
            $name === 'reference' => $this->booking->booking_reference,
            $name === 'pnr' => 'NONE',
            $name === 'slug' && str_contains($uri, 'blog') => 'crawl-post',
            $name === 'slug' && str_contains($uri, 'package') => 'crawl-tour',
            $name === 'slug' => 'crawl-hotel',
            $name === 'destination' => 'dhaka',
            $name === 'code' => 'USD',
            $name === 'lang' => 'en',
            $name === 'token' => 'abc',
            $name === 'vendorId' => $this->users['vendor']->id,
            $name === 'provider' => 'google',
            $name === 'ticket' => $firstId('support_tickets'),
            $name === 'promotion' => str_contains($uri, 'vendor') ? $firstId('coupons') : $firstId('promotions'),
            $name === 'package' => $firstId('tour_packages'),
            $name === 'deal' => $firstId('deals'),
            $name === 'cm' => $firstId('cms_contents'),
            $name === 'amenity' => $firstId('amenities'),
            $name === 'id' && str_contains($uri, 'hotels') => $this->property->id,
            $name === 'id' && str_contains($uri, 'propert') => $this->property->id,
            $name === 'id' && str_contains($uri, 'bookings') => $this->booking->id,
            $name === 'id' && str_contains($uri, 'users') => $this->users['customer']->id,
            $name === 'id' && str_contains($uri, 'blog') => $firstId('blog_posts'),
            $name === 'id' && str_contains($uri, 'email-templates') => $firstId('email_templates'),
            $name === 'id' && str_contains($uri, 'packages') => $firstId('tour_packages'),
            $name === 'id' && str_contains($uri, 'support') => $firstId('support_tickets'),
            default => 1,
        };
    }

    public function test_no_page_returns_a_server_error_for_any_role(): void
    {
        $adminSlug = env('ADMIN_LOGIN_SLUG', 'pb-control-gate-99');
        $failures = [];
        $visited = 0;

        foreach ([null, 'customer', 'vendor', 'admin', 'super_admin'] as $role) {
            foreach (Route::getRoutes() as $route) {
                if (!in_array('GET', $route->methods(), true)) {
                    continue;
                }

                $uri = $route->uri();
                if (Str::startsWith($uri, self::SKIP) || in_array($uri, self::SKIP, true)) {
                    continue;
                }

                // Staff areas only need crawling by the role that can enter them.
                $isAdminArea  = Str::startsWith($uri, 'admin');
                $isVendorArea = Str::startsWith($uri, 'vendor') && $uri !== 'vendor/login';
                if ($isAdminArea && !in_array($role, ['admin', 'super_admin'], true)) {
                    continue;
                }
                if ($isVendorArea && $role !== 'vendor') {
                    continue;
                }
                if (!$isAdminArea && !$isVendorArea && $role === 'super_admin') {
                    continue;
                }

                $path = '/' . preg_replace_callback('/\{(\w+)\??\}/', fn ($m) => (string) $this->parameterValue($m[1], $uri), $uri);

                $this->refreshApplicationState();
                if ($role) {
                    $this->actingAs($this->users[$role]->fresh());
                }

                try {
                    $response = $this->get($path);
                    $status = $response->getStatusCode();
                    $visited++;

                    if ($status >= 500) {
                        $error = $response->exception ? get_class($response->exception) . ': ' . Str::limit($response->exception->getMessage(), 260) : 'HTTP ' . $status;
                        $failures[] = sprintf('[%s] GET %s -> %d %s', $role ?? 'guest', $path, $status, $error);
                    }
                } catch (\Throwable $e) {
                    $failures[] = sprintf('[%s] GET %s -> EXCEPTION %s: %s', $role ?? 'guest', $path, get_class($e), Str::limit($e->getMessage(), 260));
                }
            }
        }

        $this->assertGreaterThan(200, $visited);
        $this->assertSame([], array_values(array_unique($failures)), "Pages with server errors:\n" . implode("\n", array_unique($failures)));
    }

    /**
     * Every form endpoint, submitted empty and submitted with junk, must answer with a
     * validation error / redirect / 4xx. A 500 means a button somewhere crashes.
     */
    public function test_no_form_submission_crashes_for_any_role(): void
    {
        $skip = ['auth/{provider}/redirect', 'auth/{provider}/callback', 'admin/system/cache-clear', 'logout', 'auth/logout', 'api/v1/auth/logout'];
        $junk = [
            'name' => str_repeat('x', 300), 'email' => 'not-an-email', 'status' => 'nonsense', 'amount' => 'abc', 'price' => -5,
            'id' => 'zzz', 'ids' => ['a', 'b'], 'action' => 'nonsense', 'payment_status' => 'nonsense', 'role' => 'emperor',
            'check_in' => 'yesterday-ish', 'check_out' => '???', 'rating' => 99, 'points' => -1, 'settings' => 'string-not-array',
            'date' => 'x', 'start_date' => 'x', 'end_date' => 'x', 'room_id' => 'x', 'property_id' => 'x', 'order' => 'x',
        ];
        $failures = [];
        $sent = 0;

        foreach ([null, 'customer', 'vendor', 'admin', 'super_admin'] as $role) {
            foreach (Route::getRoutes() as $route) {
                $methods = array_values(array_diff($route->methods(), ['GET', 'HEAD', 'OPTIONS']));
                if ($methods === []) {
                    continue;
                }

                $uri = $route->uri();
                if (in_array($uri, $skip, true) || Str::startsWith($uri, ['_ignition', 'payment/'])) {
                    continue;
                }

                $isAdminArea  = Str::startsWith($uri, 'admin');
                $isVendorArea = Str::startsWith($uri, 'vendor') && $uri !== 'vendor/login';
                if (($isAdminArea && !in_array($role, ['admin', 'super_admin'], true)) || ($isVendorArea && $role !== 'vendor')) {
                    continue;
                }
                if (!$isAdminArea && !$isVendorArea && $role === 'super_admin') {
                    continue;
                }

                $path = '/' . preg_replace_callback('/\{(\w+)\??\}/', fn ($m) => (string) $this->parameterValue($m[1], $uri), $uri);

                foreach (['empty' => [], 'junk' => $junk] as $label => $payload) {
                    $this->refreshApplicationState();
                    if ($role) {
                        $this->actingAs($this->users[$role]->fresh());
                    }

                    try {
                        $response = $this->call($methods[0], $path, $payload);
                        $sent++;
                        if ($response->getStatusCode() >= 500) {
                            $error = $response->exception ? get_class($response->exception) . ': ' . Str::limit($response->exception->getMessage(), 240) : '';
                            $failures[] = sprintf('[%s] %s %s (%s) -> %d %s', $role ?? 'guest', $methods[0], $path, $label, $response->getStatusCode(), $error);
                        }
                    } catch (\Throwable $e) {
                        $failures[] = sprintf('[%s] %s %s (%s) -> EXCEPTION %s: %s', $role ?? 'guest', $methods[0], $path, $label, get_class($e), Str::limit($e->getMessage(), 240));
                    }
                }
            }
        }

        $failures = array_values(array_unique($failures));
        $this->assertGreaterThan(200, $sent);
        $this->assertSame([], $failures, "Form endpoints that crash:\n" . implode("\n", $failures));
    }

    /** Each request starts logged-out with a clean session, like a new browser tab. */
    private function refreshApplicationState(): void
    {
        $this->app['auth']->forgetGuards();
        $this->flushSession();
    }
}
