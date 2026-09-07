<?php

namespace Tests\Feature;

use App\Models\Folio;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\HotelPaymentGateway;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialBillingAndGatewayModuleTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotel;
    private User $admin;
    private Guest $guest;
    private Reservation $reservation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::create([
            'name' => 'Grand Financial Palace',
            'slug' => 'grand-financial-palace',
            'code' => 'GFP-01',
        ]);

        $this->admin = User::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Chief Financial Officer',
            'email' => 'cfo@grandfinancial.com',
            'role' => 'HOTEL_OWNER',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $roomType = RoomType::create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Presidential Penthouse',
            'code' => 'PRES-PH',
            'base_price' => 50000.00,
            'capacity' => 4,
        ]);

        $room = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $roomType->id,
            'room_number' => '901',
            'floor' => 9,
            'status' => 'AVAILABLE',
            'cleanliness' => 'CLEAN',
        ]);

        $this->guest = Guest::create([
            'hotel_id' => $this->hotel->id,
            'first_name' => 'Alexander',
            'last_name' => 'Hamilton',
            'email' => 'alexander@treasury.gov',
            'phone' => '+12025550199',
        ]);

        $this->reservation = Reservation::create([
            'hotel_id' => $this->hotel->id,
            'guest_id' => $this->guest->id,
            'room_id' => $room->id,
            'room_type_id' => $roomType->id,
            'reservation_code' => 'RES-FIN-9901',
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(3)->toDateString(),
            'adults' => 2,
            'children' => 0,
            'status' => 'CHECKED_IN',
            'source' => 'DIRECT_WALKIN',
            'base_rate' => 50000.00,
            'total_amount' => 150000.00,
        ]);

        $folio = Folio::create([
            'hotel_id' => $this->hotel->id,
            'reservation_id' => $this->reservation->id,
            'folio_number' => 'FOL-FIN-9901',
            'status' => 'OPEN',
            'total_charges' => 150000.00,
            'total_paid' => 100000.00,
            'balance' => 50000.00,
        ]);

        Payment::create([
            'hotel_id' => $this->hotel->id,
            'folio_id' => $folio->id,
            'reservation_id' => $this->reservation->id,
            'amount' => 100000.00,
            'payment_method' => 'CREDIT_CARD',
            'transaction_reference' => 'TXN-STRIPE-778899',
            'status' => 'SUCCESS',
            'paid_at' => now(),
        ]);
    }

    public function test_can_view_transactions_and_kpis(): void
    {
        $response = $this->actingAs($this->admin)->get('/transaction.html');
        $response->assertStatus(200);
        $response->assertSee('Transaction');
        $response->assertSee('TXN-STRIPE-778899');
    }

    public function test_can_configure_payment_gateways(): void
    {
        $response = $this->actingAs($this->admin)->get('/payment-set.html');
        $response->assertStatus(200);
        $response->assertSee('Payment Settings');

        $stripeSave = $this->actingAs($this->admin)->post('/payment-set', [
            'gateway' => 'STRIPE',
            'is_enabled' => '1',
            'environment' => 'live',
            'currency_code' => 'USD',
            'publishable_key' => 'pk_live_hotelair_enterprise_key',
            'secret_key' => 'sk_live_hotelair_secret_token_live',
            'webhook_secret' => 'whsec_enterprise_webhook_secret',
        ]);

        $stripeSave->assertStatus(302);
        $this->assertDatabaseHas('hotel_payment_gateways', [
            'hotel_id' => $this->hotel->id,
            'gateway' => 'STRIPE',
            'is_enabled' => true,
            'environment' => 'live',
            'publishable_key' => 'pk_live_hotelair_enterprise_key',
        ]);
    }

    public function test_can_view_booking_reports(): void
    {
        $response = $this->actingAs($this->admin)->get('/booking-report.html');
        $response->assertStatus(200);
        $response->assertSee('Booking Reports');
        $response->assertSee('Alexander Hamilton');
    }

    public function test_can_export_booking_report_csv(): void
    {
        $response = $this->actingAs($this->admin)->get('/booking-report.html?export=csv');
        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('RES-FIN-9901', $response->streamedContent());
    }

    public function test_can_view_folio_invoice(): void
    {
        $folio = Folio::where('reservation_id', $this->reservation->id)->first();
        $response = $this->actingAs($this->admin)->get('/folio-invoice/' . $folio->id);
        $response->assertStatus(200);
        $response->assertSee('GUEST FOLIO');
        $response->assertSee('FOL-FIN-9901');
    }

    public function test_can_view_accounts_setting(): void
    {
        $response = $this->actingAs($this->admin)->get('/accounts-setting.html');
        $response->assertStatus(200);
        $response->assertSee('Account Setting');
    }
}
