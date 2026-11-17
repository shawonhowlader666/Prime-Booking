<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AccountingLedger;
use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomHold;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\UserReward;
use App\Services\Booking\BookingLifecycleService;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * End-to-end guarantees of the checkout: what the guest is shown is what is
 * charged, inventory cannot be oversold or leaked, and nobody gets a paid
 * booking without the gateway confirming the money.
 */
class CheckoutIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private Property $property;
    private Room $room;
    private string $in;
    private string $out;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        $vendor = User::create([
            'name' => 'Vendor', 'email' => 'v_' . Str::random(6) . '@example.com',
            'password' => bcrypt('Secret123!'), 'role' => 'vendor', 'status' => 'active',
        ]);

        $this->property = Property::create([
            'vendor_id' => $vendor->id, 'name' => 'Integrity Hotel', 'slug' => 'integrity-' . Str::random(6),
            'type' => 'hotel', 'city' => 'Dhaka', 'address' => 'Gulshan', 'price_per_night' => 5000,
            'commission_rate' => 10, 'status' => 'active',
        ]);

        $this->room = Room::create([
            'property_id' => $this->property->id, 'name' => 'Deluxe', 'price_per_night' => 5000,
            'total_rooms' => 3, 'max_adults' => 2, 'max_children' => 0, 'max_guests' => 2, 'status' => 'active',
        ]);

        SiteSetting::set('dynamic_weekend_pricing_enabled', '0');
        SiteSetting::set('tax_rate', '10');

        $this->in  = now()->addDays(20)->toDateString();
        $this->out = now()->addDays(22)->toDateString();
    }

    private function payload(array $overrides = []): array
    {
        return $overrides + [
            'guest_name' => 'Test Guest', 'guest_email' => 'guest@example.com', 'guest_phone' => '01711223344',
            'check_in' => $this->in, 'check_out' => $this->out, 'guests' => 2, 'rooms' => 1,
            'room_id' => $this->room->id, 'payment_method' => 'pay_at_hotel',
        ];
    }

    private function book(array $overrides = [])
    {
        return $this->postJson(route('booking.store', $this->property->id), $this->payload($overrides));
    }

    public function test_checkout_page_renders_real_property_data_and_the_charged_total(): void
    {
        $response = $this->get(route('booking.form', $this->property->id) . "?room_id={$this->room->id}&check_in={$this->in}&check_out={$this->out}&rooms=2");

        $response->assertOk()
            ->assertSee('Integrity Hotel')
            ->assertSee('2 × Deluxe')
            // 5000 x 2 nights x 2 rooms = 20,000 + 10% tax
            ->assertSee('22,000');

        $this->assertDatabaseHas('room_holds', ['room_id' => $this->room->id, 'rooms_count' => 2]);
    }

    public function test_total_is_computed_server_side_for_rooms_coupon_and_tax(): void
    {
        Coupon::create(['code' => 'SAVE10', 'type' => 'percentage', 'amount' => 10, 'min_spend' => 0, 'status' => 'active']);

        $response = $this->book(['rooms' => 2, 'coupon_code' => 'save10', 'total_price' => 1, 'subtotal' => 1]);
        $response->assertOk()->assertJson(['success' => true]);

        $booking = Booking::firstOrFail();
        $this->assertSame(2, (int) $booking->rooms_count);
        $this->assertEquals(20000.0, (float) $booking->subtotal);
        $this->assertEquals(2000.0, (float) $booking->discount_amount);
        $this->assertEquals(1800.0, (float) $booking->tax_amount);
        $this->assertEquals(19800.0, (float) $booking->total_price);
        $this->assertSame(1, (int) Coupon::where('code', 'SAVE10')->value('used_count'));
    }

    public function test_quote_endpoint_matches_what_the_booking_charges(): void
    {
        $quote = $this->postJson(route('checkout.quote'), [
            'property_id' => $this->property->id, 'room_id' => $this->room->id,
            'check_in' => $this->in, 'check_out' => $this->out, 'rooms' => 1, 'guests' => 2,
            'addons' => ['spa_package'],
        ])->assertOk()->json('quote');

        $this->assertArrayNotHasKey('commission_amount', $quote);

        $this->book(['addons' => ['spa_package']])->assertOk();

        $this->assertEquals($quote['total'], (float) Booking::firstOrFail()->total_price);
        $this->assertEquals(13200.0, $quote['total']); // (10,000 + 2,000) + 10%
    }

    public function test_invalid_coupon_is_rejected_instead_of_silently_charging_full_price(): void
    {
        $this->book(['coupon_code' => 'NOPE'])->assertStatus(422)->assertJson(['success' => false]);
        $this->assertSame(0, Booking::count());
    }

    public function test_inventory_counts_rooms_not_bookings_and_blocks_overselling(): void
    {
        $this->book(['rooms' => 2])->assertOk();
        $this->book(['rooms' => 2, 'guest_email' => 'b@example.com'])->assertStatus(422);
        $this->book(['rooms' => 1, 'guest_email' => 'c@example.com'])->assertOk();
        $this->book(['rooms' => 1, 'guest_email' => 'd@example.com'])->assertStatus(422);

        $this->assertSame(3, (int) Booking::whereIn('status', ['pending', 'confirmed'])->sum('rooms_count'));
    }

    public function test_abandoned_checkout_holds_expire_and_never_leak_inventory(): void
    {
        $inventory = app(InventoryService::class);

        foreach (range(1, 3) as $i) {
            $this->assertTrue($inventory->holdRoom($this->room->id, $this->in, $this->out, "abandoned-{$i}", 15));
        }
        $this->assertFalse($inventory->checkAvailability($this->room->id, $this->in, $this->out)['is_available']);

        // A guest's own hold never counts against them.
        $this->assertTrue($inventory->checkAvailability($this->room->id, $this->in, $this->out, 1, 'abandoned-1')['is_available']);

        RoomHold::query()->update(['expires_at' => now()->subMinute()]);
        $this->assertSame(3, $inventory->checkAvailability($this->room->id, $this->in, $this->out)['min_available']);
        $this->assertSame(3, $inventory->pruneExpiredHolds());
    }

    public function test_double_submit_with_same_idempotency_token_creates_one_booking(): void
    {
        $token = Str::random(40);

        $first  = $this->book(['idempotency_token' => $token])->assertOk()->json('booking_reference');
        $second = $this->book(['idempotency_token' => $token])->assertOk()->json('booking_reference');

        $this->assertSame($first, $second);
        $this->assertSame(1, Booking::count());
    }

    public function test_guest_capacity_is_enforced(): void
    {
        $this->book(['guests' => 5])->assertStatus(422);
        $this->book(['guests' => 4, 'rooms' => 2])->assertOk();
    }

    public function test_online_booking_stays_pending_until_gateway_confirms_and_settles_once(): void
    {
        $ref = $this->book(['payment_method' => 'sslcommerz'])->assertOk()->json('booking_reference');
        $booking = Booking::where('booking_reference', $ref)->firstOrFail();

        $this->assertSame('pending', $booking->status);
        $this->assertSame('pending', $booking->payment_status);
        $this->assertNotNull($booking->payment_expires_at);

        // A forged callback with no verifiable val_id changes nothing.
        $this->post(route('payment.ssl.success'), ['tran_id' => $ref . '_FORGED', 'val_id' => 'fake', 'status' => 'VALID']);
        $this->post(route('payment.ssl.ipn'), ['tran_id' => $ref . '_FORGED', 'val_id' => 'fake', 'status' => 'VALID']);
        $this->assertSame('pending', $booking->fresh()->payment_status);

        // A fail/cancel callback does not let a stranger cancel the booking either.
        $this->post(route('payment.ssl.fail'), ['tran_id' => $ref . '_X']);
        $this->assertSame('pending', $booking->fresh()->status);

        $lifecycle = app(BookingLifecycleService::class);
        $this->assertSame(BookingLifecycleService::PAID, $lifecycle->confirmPayment($booking, 'sslcommerz', 'BANK-1', 11000.0));
        $this->assertSame(BookingLifecycleService::ALREADY_PAID, $lifecycle->confirmPayment($booking, 'sslcommerz', 'BANK-1', 11000.0));

        $booking->refresh();
        $this->assertSame('confirmed', $booking->status);
        $this->assertSame('paid', $booking->payment_status);
        $this->assertSame(1, AccountingLedger::where('booking_id', $booking->id)->where('type', 'credit')->count());
    }

    public function test_underpaid_gateway_amount_does_not_confirm_the_booking(): void
    {
        $ref = $this->book(['payment_method' => 'sslcommerz'])->json('booking_reference');
        $booking = Booking::where('booking_reference', $ref)->firstOrFail();

        $outcome = app(BookingLifecycleService::class)->confirmPayment($booking, 'sslcommerz', 'BANK-2', 10.0);

        $this->assertSame(BookingLifecycleService::AMOUNT_MISMATCH, $outcome);
        $this->assertSame('pending', $booking->fresh()->payment_status);
    }

    public function test_simulated_payment_pages_do_not_exist_in_production(): void
    {
        $ref = $this->book(['payment_method' => 'sslcommerz'])->json('booking_reference');

        $this->app->detectEnvironment(fn () => 'production');

        $this->post(route('payment.ssl.sandbox-execute', $ref))->assertNotFound();
        $this->post(route('payment.bkash.sandbox-execute', $ref))->assertNotFound();
        $this->get(route('payment.bkash.callback', ['paymentID' => 'BKASH-DEMO-123', 'status' => 'success', 'reference' => $ref]));
        $this->postJson('/api/v1/payment/callback/bkash', ['tran_id' => $ref, 'status' => 'success'])->assertStatus(400);

        $this->assertSame('pending', Booking::where('booking_reference', $ref)->value('payment_status'));
    }

    public function test_unpaid_online_booking_expires_and_releases_room_coupon_and_points(): void
    {
        $user = User::create([
            'name' => 'Member', 'email' => 'member@example.com', 'password' => bcrypt('Secret123!'),
            'role' => 'customer', 'status' => 'active',
        ]);
        UserReward::create(['user_id' => $user->id, 'points_balance' => 500, 'total_earned_points' => 500, 'total_redeemed_points' => 0]);
        Coupon::create(['code' => 'ONCE', 'type' => 'fixed', 'amount' => 500, 'min_spend' => 0, 'status' => 'active', 'usage_limit' => 1]);

        $ref = $this->actingAs($user)->book([
            'payment_method' => 'sslcommerz', 'rooms' => 3, 'guests' => 6, 'coupon_code' => 'ONCE', 'redeem_points' => 200,
            'guest_email' => 'member@example.com',
        ])->assertOk()->json('booking_reference');

        $booking = Booking::where('booking_reference', $ref)->firstOrFail();
        $this->assertSame(200, (int) $booking->points_redeemed);
        $this->assertSame(300, (int) UserReward::where('user_id', $user->id)->value('points_balance'));
        $this->assertFalse(app(InventoryService::class)->checkAvailability($this->room->id, $this->in, $this->out)['is_available']);

        $booking->update(['payment_expires_at' => now()->subMinute()]);

        // Availability frees up the moment the window closes, even before the scheduler runs.
        $this->assertTrue(app(InventoryService::class)->checkAvailability($this->room->id, $this->in, $this->out, 3)['is_available']);

        $this->artisan('bookings:expire-pending')->assertSuccessful();

        $booking->refresh();
        $this->assertSame('cancelled', $booking->status);
        $this->assertSame('expired', $booking->payment_status);
        $this->assertSame(500, (int) UserReward::where('user_id', $user->id)->value('points_balance'));
        $this->assertSame(0, (int) Coupon::where('code', 'ONCE')->value('used_count'));
    }

    public function test_guest_cannot_cancel_someone_elses_booking_with_only_a_reference(): void
    {
        $ref = $this->book()->json('booking_reference');
        $this->flushSession();

        $this->post(route('booking.cancel', $ref))->assertForbidden();
        $this->assertSame('confirmed', Booking::where('booking_reference', $ref)->value('status'));
    }

    public function test_owner_can_cancel_before_check_in_but_not_on_the_day(): void
    {
        $ref = $this->book()->json('booking_reference');

        $this->post(route('booking.cancel', $ref))->assertRedirect();
        $this->assertSame('cancelled', Booking::where('booking_reference', $ref)->value('status'));

        $today = $this->book(['check_in' => now()->toDateString(), 'check_out' => now()->addDay()->toDateString(), 'guest_email' => 'today@example.com'])
            ->assertOk()->json('booking_reference');

        $this->post(route('booking.cancel', $today));
        $this->assertSame('confirmed', Booking::where('booking_reference', $today)->value('status'));
    }

    public function test_vendor_balance_only_counts_money_the_platform_actually_collected(): void
    {
        $vendorId   = (int) $this->property->vendor_id;
        $accounting = app(\App\Services\AccountingService::class);
        $past       = ['check_in' => now()->subDays(5)->toDateString(), 'check_out' => now()->subDays(3)->toDateString()];
        $base       = [
            'property_id' => $this->property->id, 'room_id' => $this->room->id, 'guest_name' => 'G', 'guest_email' => 'g@example.com',
            'guest_phone' => '01700000000', 'guests' => 1, 'nights' => 2, 'total_price' => 10000, 'total_amount' => 10000, 'currency' => 'BDT',
        ];

        // Never paid: no balance, whatever its status says.
        Booking::create($base + $past + ['booking_reference' => 'BAL-UNPAID', 'payment_method' => 'sslcommerz', 'payment_status' => 'pending', 'status' => 'confirmed']);
        $this->assertEquals(0.0, $accounting->getSingleVendorAccounting($vendorId)['withdrawable_balance']);

        // Paid online but the stay is still in the future: held in escrow, not withdrawable.
        Booking::create($base + ['booking_reference' => 'BAL-FUTURE', 'check_in' => $this->in, 'check_out' => $this->out, 'payment_method' => 'sslcommerz', 'payment_status' => 'paid', 'status' => 'confirmed']);
        $acc = $accounting->getSingleVendorAccounting($vendorId);
        $this->assertEquals(0.0, $acc['withdrawable_balance']);
        $this->assertEquals(9000.0, $acc['escrow_future_balance']);

        // Paid online and completed: 10,000 less 10% commission.
        Booking::create($base + $past + ['booking_reference' => 'BAL-DONE', 'payment_method' => 'sslcommerz', 'payment_status' => 'paid', 'status' => 'completed']);
        $this->assertEquals(9000.0, $accounting->getSingleVendorAccounting($vendorId)['withdrawable_balance']);

        // Paid at the hotel: the hotel already has the cash and owes the platform its 10%.
        Booking::create($base + $past + ['booking_reference' => 'BAL-HOTEL', 'payment_method' => 'pay_at_hotel', 'payment_status' => 'paid', 'status' => 'completed']);
        $this->assertEquals(8000.0, $accounting->getSingleVendorAccounting($vendorId)['withdrawable_balance']);
    }

    public function test_vendor_cannot_mark_an_online_booking_as_paid(): void
    {
        $ref     = $this->book(['payment_method' => 'sslcommerz'])->json('booking_reference');
        $booking = Booking::where('booking_reference', $ref)->firstOrFail();
        $vendor  = User::findOrFail($this->property->vendor_id);

        $this->actingAs($vendor)->post(route('vendor.bookings.update-payment', $booking->id), ['payment_status' => 'paid']);
        $this->actingAs($vendor)->post(route('vendor.bookings.update-status', $ref), ['status' => 'confirmed']);

        $booking->refresh();
        $this->assertSame('pending', $booking->payment_status);
        $this->assertSame('pending', $booking->status);
        $this->assertSame(0, AccountingLedger::where('booking_id', $booking->id)->count());
    }

    public function test_vendor_cannot_reply_to_another_vendors_inquiry(): void
    {
        $inquiry = \App\Models\Inquiry::create([
            'name' => 'Guest', 'email' => 'guest@example.com', 'phone' => '01700000000',
            'message' => 'Hello', 'property_id' => $this->property->id,
        ]);

        $other = User::create([
            'name' => 'Other Vendor', 'email' => 'other2@example.com', 'password' => bcrypt('Secret123!'),
            'role' => 'vendor', 'status' => 'active',
        ]);

        $this->actingAs($other)->post(route('vendor.inquiries.reply', $inquiry->id), ['reply' => 'hijacked'])->assertNotFound();
        $this->assertNotSame('hijacked', $inquiry->fresh()->reply);
    }

    public function test_setting_a_price_on_the_calendar_does_not_shrink_inventory(): void
    {
        $vendor = User::findOrFail($this->property->vendor_id);
        $in = now()->addDays(20)->toDateString();

        $this->actingAs($vendor)->post(route("vendor.availability.update-range"), [
            "room_id" => $this->room->id, "start_date" => $in, "end_date" => $in, "price" => 7000, "is_blocked" => 0,
        ])->assertRedirect();

        $check = app(InventoryService::class)->checkAvailability($this->room->id, $in, now()->addDays(21)->toDateString(), 3);
        $this->assertTrue($check["is_available"], "All 3 rooms must still be sellable after a price change.");

        // Updating only the block flag keeps the custom price.
        $this->actingAs($vendor)->post(route("vendor.availability.update-range"), [
            "room_id" => $this->room->id, "start_date" => $in, "end_date" => $in, "is_blocked" => 1,
        ]);
        $row = \App\Models\RoomAvailability::where("room_id", $this->room->id)->firstOrFail();
        $this->assertEquals(7000.0, (float) $row->price);
        $this->assertTrue((bool) $row->is_blocked);
        $this->assertFalse(app(InventoryService::class)->checkAvailability($this->room->id, $in, now()->addDays(21)->toDateString())["is_available"]);
    }

    public function test_browsing_does_not_use_up_the_booking_or_login_rate_limits(): void
    {
        // A guest typing in the search box fires many autocomplete requests.
        for ($i = 0; $i < 40; $i++) {
            $this->get('/api/search/autocomplete?q=dha' . $i);
        }

        $this->book()->assertOk();
        $this->post(route('login.post'), ['email' => 'nobody@example.com', 'password' => 'x'])->assertStatus(302);
    }

    public function test_vendor_cannot_export_or_import_ical_for_another_vendors_room(): void
    {
        $other = User::create([
            'name' => 'Other Vendor', 'email' => 'other@example.com', 'password' => bcrypt('Secret123!'),
            'role' => 'vendor', 'status' => 'active',
        ]);

        $this->actingAs($other)->get(route('vendor.rooms.ical.export', $this->room->id))->assertForbidden();
        $this->actingAs($other)->post(route('vendor.rooms.ical.import', $this->room->id), ['ics_url' => 'https://example.com/a.ics'])->assertForbidden();
    }
}
