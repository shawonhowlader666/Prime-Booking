<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * What an admin or vendor enters for a hotel and its rooms is exactly what is
 * stored, and exactly what guests see on the search and detail pages.
 */
class ListingFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $vendor;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        Storage::fake('public');

        $this->admin  = $this->user('admin');
        $this->vendor = $this->user('vendor');
    }

    private function user(string $role): User
    {
        return User::create([
            'name' => ucfirst($role) . ' ' . Str::random(4), 'email' => $role . '_' . Str::random(6) . '@example.com',
            'password' => bcrypt('Secret123!'), 'role' => $role, 'status' => 'active',
        ]);
    }

    private function hotelForm(array $overrides = []): array
    {
        return $overrides + [
            'name' => 'Lagoon View Hotel', 'type' => 'hotel', 'city' => "Cox's Bazar", 'star_rating' => 4,
            'address' => 'Kolatoli Road', 'price_per_night' => 6000,
            'description' => 'A quiet beachfront hotel with a pool and a rooftop restaurant.',
            'amenities' => ['Free Wi-Fi', 'Swimming Pool'],
            'free_cancellation' => '0', 'no_credit_card_required' => '0', 'is_sold_out' => '0',
        ];
    }

    private function listing(array $overrides = []): Property
    {
        $property = Property::create($overrides + [
            'vendor_id' => $this->vendor->id, 'name' => 'Seeded Hotel', 'slug' => 'seeded-' . Str::random(6),
            'type' => 'hotel', 'city' => 'Dhaka', 'address' => 'Gulshan 2', 'price_per_night' => 5000,
            'star_rating' => 4, 'status' => 'active', 'rating_score' => 0, 'total_reviews' => 0,
            'description' => 'Seeded listing used by the listing flow tests.',
        ]);

        Room::create([
            'property_id' => $property->id, 'name' => 'Deluxe King', 'price_per_night' => 5000, 'total_rooms' => 2,
            'max_adults' => 2, 'max_children' => 0, 'max_guests' => 2, 'status' => 'active',
        ]);

        return $property;
    }

    // ── Admin ────────────────────────────────────────────────────────────

    public function test_admin_creates_a_listing_with_rooms_and_uploaded_photos(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.properties.store'), $this->hotelForm([
            'commission_rate' => 9.5,
            'latitude' => '21.4272', 'longitude' => '92.0058',
            'new_gallery_category' => 'facilities',
            'gallery_image_files' => [UploadedFile::fake()->image('pool.jpg', 800, 600)],
            'primary_image_file' => UploadedFile::fake()->image('cover.jpg', 800, 600),
            'room_type' => ['Deluxe King', ''], 'room_price' => [6000, ''], 'room_qty' => [4, ''], 'room_beds' => [2, ''],
        ]));

        $response->assertRedirect(route('admin.properties.index'));

        $property = Property::where('name', 'Lagoon View Hotel')->firstOrFail();

        $this->assertSame('lagoon-view-hotel', $property->slug);
        $this->assertSame('active', $property->status);
        $this->assertEquals(9.5, (float) $property->commission_rate);
        $this->assertFalse($property->free_cancellation);
        $this->assertEquals(0.0, (float) $property->rating_score, 'A new listing has no invented rating.');
        $this->assertSame(0, (int) $property->total_reviews);

        // Uploads are stored as relative paths that still resolve after a domain or CDN change.
        $this->assertStringStartsWith('/storage/uploads/properties/', $property->primary_image);
        $gallery = $property->images;
        $this->assertCount(1, $gallery);
        $this->assertSame('facilities', $gallery[0]['category']);
        $this->assertStringStartsWith('/storage/uploads/properties/gallery/', $gallery[0]['url']);
        Storage::disk('public')->assertExists(Str::after($gallery[0]['url'], '/storage/'));

        $rooms = Room::where('property_id', $property->id)->get();
        $this->assertCount(1, $rooms, 'Blank room rows are ignored.');
        $this->assertSame(4, (int) $rooms[0]->total_rooms);
    }

    public function test_admin_edit_can_untick_checkboxes_and_clear_optional_fields(): void
    {
        $property = $this->listing([
            'free_cancellation' => true, 'no_credit_card_required' => true, 'contact_phone' => '01700000000',
            'nearest_landmark' => 'Old Landmark', 'original_price' => 9000, 'house_rules' => 'No parties',
        ]);

        $this->actingAs($this->admin)->put(route('admin.properties.update', $property->id), $this->hotelForm([
            'name' => $property->name, 'price_per_night' => 5000,
            'contact_phone' => '', 'nearest_landmark' => '', 'original_price' => '', 'house_rules' => '',
        ]))->assertRedirect();

        $property->refresh();
        $this->assertFalse($property->free_cancellation);
        $this->assertFalse($property->no_credit_card_required);
        $this->assertNull($property->contact_phone);
        $this->assertNull($property->nearest_landmark);
        $this->assertNull($property->original_price);
        $this->assertNull($property->house_rules);
    }

    public function test_gallery_edit_recategorises_deletes_and_rejects_script_urls(): void
    {
        $property = $this->listing(['images' => [
            ['url' => 'https://cdn.example.com/a.jpg', 'category' => 'property_views', 'title' => 'A'],
            ['url' => 'https://cdn.example.com/b.jpg', 'category' => 'property_views', 'title' => 'B'],
        ]]);

        $this->actingAs($this->admin)->put(route('admin.properties.update', $property->id), $this->hotelForm([
            'name' => $property->name,
            'existing_gallery' => [
                ['url' => 'https://cdn.example.com/a.jpg', 'category' => 'dining', 'title' => 'Restaurant'],
                ['url' => 'https://cdn.example.com/b.jpg', 'category' => 'rooms', 'title' => 'B', 'delete' => '1'],
                ['url' => 'javascript:alert(1)', 'category' => 'rooms', 'title' => 'x'],
            ],
            'gallery_images' => "https://cdn.example.com/c.jpg\nnot-a-url\njavascript:alert(2)",
            'new_gallery_category' => 'rooms',
        ]))->assertRedirect();

        $gallery = $property->fresh()->images;

        $this->assertSame(['https://cdn.example.com/a.jpg', 'https://cdn.example.com/c.jpg'], array_column($gallery, 'url'));
        $this->assertSame(['dining', 'rooms'], array_column($gallery, 'category'));
        $this->assertSame('Restaurant', $gallery[0]['title']);
    }

    public function test_listing_cannot_be_approved_without_a_bookable_room(): void
    {
        $property = Property::create([
            'vendor_id' => $this->vendor->id, 'name' => 'Empty Hotel', 'slug' => 'empty-hotel', 'type' => 'hotel',
            'city' => 'Dhaka', 'address' => 'x', 'price_per_night' => 3000, 'status' => 'pending',
        ]);

        $this->actingAs($this->admin)->post(route('admin.properties.approve', $property->id));
        $this->assertSame('pending', $property->fresh()->status);

        Room::create(['property_id' => $property->id, 'name' => 'Std', 'price_per_night' => 3000, 'total_rooms' => 1, 'max_adults' => 2, 'max_guests' => 2, 'status' => 'active']);

        $this->actingAs($this->admin)->post(route('admin.properties.approve', $property->id));
        $this->assertSame('active', $property->fresh()->status);
    }

    // ── Vendor ───────────────────────────────────────────────────────────

    public function test_vendor_cannot_write_ratings_badges_status_or_commission(): void
    {
        $this->actingAs($this->vendor)->post(route('vendor.properties.store'), $this->hotelForm([
            'rating_score' => 9.9, 'total_reviews' => 5000, 'location_score' => 9.9, 'rooms_left' => 1,
            'ribbon_badge' => 'Best hotel in the world', 'is_featured' => 1, 'status' => 'active',
            'commission_rate' => 0, 'vendor_id' => $this->admin->id,
        ]))->assertRedirect();

        $property = Property::where('name', 'Lagoon View Hotel')->firstOrFail();

        $this->assertSame('pending', $property->status);
        $this->assertSame($this->vendor->id, (int) $property->vendor_id);
        $this->assertEquals(0.0, (float) $property->rating_score);
        $this->assertSame(0, (int) $property->total_reviews);
        $this->assertNull($property->ribbon_badge);
        $this->assertFalse((bool) $property->is_featured);
        $this->assertGreaterThan(0, (float) $property->commission_rate);
        $this->assertSame(0, Room::where('property_id', $property->id)->count(), 'No placeholder room is invented.');

        // Same on edit.
        $property->update(['status' => 'active', 'rating_score' => 0, 'total_reviews' => 0]);
        $this->actingAs($this->vendor)->put(route('vendor.properties.update', $property->id), $this->hotelForm([
            'rating_score' => 9.9, 'total_reviews' => 5000, 'ribbon_badge' => 'Top', 'commission_rate' => 0,
        ]))->assertRedirect();

        $property->refresh();
        $this->assertEquals(0.0, (float) $property->rating_score);
        $this->assertNull($property->ribbon_badge);
        $this->assertGreaterThan(0, (float) $property->commission_rate);
    }

    public function test_vendor_room_form_saves_every_field_and_can_clear_them(): void
    {
        $property = $this->listing();
        $base = route('vendor.rooms.store', $property->id);

        $this->actingAs($this->vendor)->post($base, [
            '_full_form' => 1, 'name' => 'Ocean Suite', 'bed_type' => '1 King Bed', 'price_per_night' => 9000,
            'max_adults' => 2, 'max_children' => 1, 'total_rooms' => 3, 'room_size_sqm' => 45,
            'view_type' => 'Sea View', 'amenities' => ['Air conditioning', 'Mini bar'], 'facilities_text' => "Bathtub\nBalcony",
            'bathroom_features' => ['Hot water'], 'breakfast_included' => '1', 'free_cancellation' => '1', 'extra_bed_allowed' => '0',
            'image_files' => [UploadedFile::fake()->image('suite.jpg', 600, 400)],
            'image_urls' => "https://cdn.example.com/suite2.jpg\njavascript:alert(1)",
        ])->assertRedirect();

        $room = Room::where('name', 'Ocean Suite')->firstOrFail();
        $stored = json_decode($room->getRawOriginal('images'), true);

        $this->assertSame(3, (int) $room->max_guests);
        $this->assertSame(3, (int) $room->total_rooms);
        $this->assertSame(['Air conditioning', 'Mini bar', 'Bathtub', 'Balcony'], $room->facilities);
        $this->assertTrue($room->breakfast_included);
        $this->assertCount(2, $stored);
        $this->assertStringStartsWith('/storage/uploads/rooms/', $stored[0]);
        $this->assertSame('https://cdn.example.com/suite2.jpg', $stored[1]);

        // Edit: untick, clear lists, delete one photo (the form posts the resolved URL).
        $this->actingAs($this->vendor)->put(route('vendor.rooms.update', [$property->id, $room->id]), [
            '_full_form' => 1, 'name' => 'Ocean Suite', 'bed_type' => '1 King Bed', 'price_per_night' => 9500,
            'max_adults' => 2, 'max_children' => 0, 'total_rooms' => 3,
            'breakfast_included' => '0', 'free_cancellation' => '0', 'extra_bed_allowed' => '0',
            'delete_images' => [$room->images[0]],
        ])->assertRedirect();

        $room->refresh();
        $this->assertFalse($room->breakfast_included);
        $this->assertFalse($room->free_cancellation);
        $this->assertSame([], $room->facilities);
        $this->assertSame(2, (int) $room->max_guests);
        $this->assertSame(['https://cdn.example.com/suite2.jpg'], json_decode($room->getRawOriginal('images'), true));
    }

    public function test_upload_named_php_is_never_stored_with_a_php_extension(): void
    {
        $property = $this->listing();

        $this->actingAs($this->vendor)->post(route('vendor.rooms.store', $property->id), [
            'name' => 'Trick Room', 'bed_type' => 'Double', 'price_per_night' => 3000, 'max_adults' => 2, 'total_rooms' => 1,
            'image_files' => [UploadedFile::fake()->image('shell.php', 100, 100)],
        ]);

        $files = Storage::disk('public')->allFiles();
        $this->assertSame([], array_values(array_filter($files, fn ($f) => str_ends_with(strtolower($f), '.php'))));

        // Whether the upload was rejected or accepted, nothing executable was written.
        $room = Room::where('name', 'Trick Room')->first();
        if ($room) {
            foreach ((array) json_decode((string) $room->getRawOriginal('images'), true) as $stored) {
                $this->assertMatchesRegularExpression('/\.(webp|jpe?g|png)$/', $stored);
            }
        }
    }

    // ── Guest-facing pages ───────────────────────────────────────────────

    public function test_unknown_hotel_is_a_404_not_some_other_hotel(): void
    {
        $this->listing();

        $this->get('/hotels/999999')->assertNotFound();
        $this->get('/hotel/no-such-hotel')->assertNotFound();
        $this->get('/hotels/999999/brochure')->assertNotFound();
    }

    public function test_detail_page_shows_the_listing_data_and_books_the_selected_dates(): void
    {
        $property = $this->listing(['name' => 'Harbour Lights Hotel', 'amenities' => ['Rooftop Pool']]);
        $room = $property->rooms()->first();
        $in  = now()->addDays(10)->toDateString();
        $out = now()->addDays(12)->toDateString();

        $response = $this->get("/hotels/{$property->id}?check_in={$in}&check_out={$out}&guests=2");

        $response->assertOk()
            ->assertSee('Harbour Lights Hotel')
            ->assertSee('Deluxe King')
            ->assertSee('Rooftop Pool')
            ->assertSee("room_id={$room->id}&check_in={$in}&check_out={$out}&guests=2", false)
            ->assertDontSee('aggregateRating', false)
            ->assertDontSee('% off today', false);

        // Garbage dates never crash the page.
        $this->get("/hotels/{$property->id}?check_in=not-a-date&check_out=2020-01-01")->assertOk();
    }

    public function test_detail_page_marks_a_fully_booked_room_as_sold_out_for_those_dates(): void
    {
        $property = $this->listing();
        $room = $property->rooms()->first();
        $in  = now()->addDays(10)->toDateString();
        $out = now()->addDays(12)->toDateString();

        Booking::create([
            'booking_reference' => 'FULL-1', 'property_id' => $property->id, 'room_id' => $room->id, 'guest_name' => 'G',
            'guest_email' => 'g@example.com', 'guest_phone' => '01700000000', 'check_in' => $in, 'check_out' => $out,
            'guests' => 2, 'rooms_count' => 2, 'nights' => 2, 'total_price' => 10000, 'total_amount' => 10000,
            'payment_method' => 'pay_at_hotel', 'payment_status' => 'unpaid', 'status' => 'confirmed',
        ]);

        $this->get("/hotels/{$property->id}?check_in={$in}&check_out={$out}")
            ->assertOk()
            ->assertSee('agoda-soldout-room-card', false);

        $later = now()->addDays(30)->toDateString();
        $this->get("/hotels/{$property->id}?check_in={$later}&check_out=" . now()->addDays(31)->toDateString())
            ->assertOk()
            ->assertSee("room_id={$room->id}", false);
    }

    public function test_search_card_shows_database_values_not_values_keyed_on_the_hotel_name(): void
    {
        $this->listing([
            'name' => 'Sea Pearl Budget Inn', 'city' => 'Dhaka', 'price_per_night' => 4321, 'star_rating' => 2,
            'ribbon_badge' => 'Editor pick', 'feature_snippet' => 'Five minutes from the airport',
        ]);

        $this->get('/search?destination=Dhaka')
            ->assertOk()
            ->assertSee('Sea Pearl Budget Inn')
            ->assertSee('4,321')
            ->assertSee('Editor pick')
            ->assertSee('Five minutes from the airport')
            ->assertSee('No reviews yet')
            ->assertDontSee('8,500')
            ->assertDontSee('pix8.agoda.net', false);
    }

    public function test_search_with_no_matching_listing_shows_no_invented_hotels(): void
    {
        $this->get("/search?destination=Cox%27s+Bazar")
            ->assertOk()
            ->assertDontSee('Sea Pearl Beach Resort');
    }

    // ── Reviews ──────────────────────────────────────────────────────────

    public function test_only_a_guest_who_stayed_can_review_and_the_score_follows_real_reviews(): void
    {
        $property = $this->listing();
        $guest    = $this->user('customer');
        $stranger = $this->user('customer');
        $url      = route('hotels.review.store', $property->id);
        $payload  = ['rating' => 4, 'comment' => 'Clean room and friendly staff.'];

        $this->post($url, $payload);
        $this->actingAs($stranger)->post($url, $payload);
        $this->assertSame(0, Review::count(), 'Guests without a stay cannot review.');

        Booking::create([
            'booking_reference' => 'STAY-1', 'property_id' => $property->id, 'user_id' => $guest->id, 'guest_name' => 'G',
            'guest_email' => $guest->email, 'guest_phone' => '01700000000',
            'check_in' => now()->subDays(3)->toDateString(), 'check_out' => now()->subDays(1)->toDateString(),
            'guests' => 2, 'nights' => 2, 'total_price' => 10000, 'total_amount' => 10000,
            'payment_status' => 'paid', 'status' => 'completed',
        ]);

        $this->actingAs($guest)->post($url, $payload);
        $this->actingAs($guest)->post($url, ['rating' => 5, 'comment' => 'Trying to vote twice.']);

        $this->assertSame(1, Review::count());
        $property->refresh();
        $this->assertSame(1, (int) $property->total_reviews);
        $this->assertEquals(8.0, (float) $property->rating_score, '4 of 5 stars is 8.0 on the 10-point scale.');

        Review::query()->first()->delete();
        $property->refresh();
        $this->assertSame(0, (int) $property->total_reviews);
        $this->assertEquals(0.0, (float) $property->rating_score, 'No reviews means no score.');
    }
    // ── Accounts ─────────────────────────────────────────────────────────

    public function test_registration_sends_a_verification_link_that_verifies_the_email(): void
    {
        \Illuminate\Support\Facades\Notification::fake();

        $this->post(route("register.post"), [
            "name" => "New Guest", "email" => "new.guest@example.com",
            "password" => "Secret1234", "password_confirmation" => "Secret1234",
        ])->assertRedirect();

        $user = User::where("email", "new.guest@example.com")->firstOrFail();
        $this->assertNull($user->email_verified_at);
        \Illuminate\Support\Facades\Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);

        $url = \Illuminate\Support\Facades\URL::temporarySignedRoute("verification.verify", now()->addHour(), [
            "id" => $user->id, "hash" => sha1($user->email),
        ]);
        $this->actingAs($user)->get($url)->assertRedirect(route("verification.notice"));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_admin_cannot_delete_a_booking_that_has_payment_records(): void
    {
        $property = $this->listing();
        $base = [
            "property_id" => $property->id, "guest_name" => "G", "guest_email" => "g@example.com", "guest_phone" => "01700000000",
            "check_in" => now()->subDays(5)->toDateString(), "check_out" => now()->subDays(3)->toDateString(),
            "guests" => 1, "nights" => 2, "total_price" => 1000, "total_amount" => 1000,
        ];
        $paid    = Booking::create($base + ["booking_reference" => "DEL-PAID", "payment_status" => "paid", "status" => "completed"]);
        $expired = Booking::create($base + ["booking_reference" => "DEL-EXP", "payment_status" => "expired", "status" => "cancelled"]);

        $this->actingAs($this->admin)->delete(route("admin.bookings.destroy", $paid->id));
        $this->actingAs($this->admin)->delete(route("admin.bookings.destroy", $expired->id));

        $this->assertNotNull(Booking::find($paid->id));
        $this->assertNull(Booking::find($expired->id));
    }

    public function test_set_password_command_creates_staff_without_passwords_in_code(): void
    {
        $this->artisan("users:set-password", ["email" => "ops@example.com", "--role" => "admin", "--generate" => true])
            ->assertSuccessful();

        $user = User::where("email", "ops@example.com")->firstOrFail();
        $this->assertSame("admin", $user->role);
        $this->assertFalse(\Illuminate\Support\Facades\Hash::check("password", $user->password));
    }
}
