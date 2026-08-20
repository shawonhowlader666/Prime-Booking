<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use App\Models\Property;
use App\Models\UserReward;
use App\Models\RewardPayoutRequest;
use App\Services\RewardPointService;

class RewardsLoyaltyEngineTest extends TestCase
{
    /** @test */
    public function reward_point_service_calculates_points_and_valuation_correctly()
    {
        $service = app(RewardPointService::class);

        // 1,000 BDT = 1 Point
        $this->assertEquals(1, $service->calculatePoints(1000));
        $this->assertEquals(5, $service->calculatePoints(5000));
        $this->assertEquals(12, $service->calculatePoints(12500));

        // 1 Point = 10 BDT
        $this->assertEquals(10.0, $service->convertPointsToBdt(1));
        $this->assertEquals(50.0, $service->convertPointsToBdt(5));
        $this->assertEquals(1000.0, $service->convertPointsToBdt(100));
    }

    /** @test */
    public function user_accumulates_points_on_completed_booking()
    {
        $user = User::first() ?? User::create([
            'name'     => 'Test Reward User',
            'email'    => 'rewarduser' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
        ]);

        $property = Property::first();
        if (!$property) {
            $this->markTestSkipped('No properties in database for booking test.');
        }

        $booking = Booking::create([
            'booking_reference' => 'TEST-RWD-' . rand(1000, 9999),
            'property_id'       => $property->id,
            'user_id'           => $user->id,
            'guest_name'        => 'John Doe',
            'guest_email'       => 'john@example.com',
            'guest_phone'       => '01711111111',
            'check_in'          => now()->addDays(2)->toDateString(),
            'check_out'         => now()->addDays(4)->toDateString(),
            'guests'            => 2,
            'nights'            => 2,
            'price_per_night'   => 5000,
            'subtotal'          => 10000,
            'tax_amount'        => 750,
            'total_price'       => 10750,
            'total_amount'      => 10750,
            'currency'          => 'BDT',
            'payment_method'    => 'bkash',
            'payment_status'    => 'paid',
            'status'            => 'confirmed',
        ]);

        $service = app(RewardPointService::class);
        $earned = $service->creditBookingPoints($booking);

        $this->assertEquals(10, $earned); // floor(10750 / 1000) = 10 pts

        $summary = $service->getUserRewardSummary($user);
        $this->assertGreaterThanOrEqual(10, $summary['points_balance']);
        $this->assertGreaterThanOrEqual(100.0, $summary['bdt_value']);
    }

    /** @test */
    public function user_can_withdraw_cash_when_reaching_100_points_threshold()
    {
        $user = User::first() ?? User::create([
            'name'     => 'Test Payout User',
            'email'    => 'payoutuser' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
        ]);

        $service = app(RewardPointService::class);

        // Grant 150 points directly
        $wallet = UserReward::firstOrCreate(['user_id' => $user->id]);
        $wallet->update([
            'points_balance'        => 150,
            'total_earned_points'   => 150,
            'total_redeemed_points' => 0,
        ]);

        $summary = $service->getUserRewardSummary($user);
        $this->assertTrue($summary['can_withdraw']);
        $this->assertEquals(1500.0, $summary['bdt_value']);

        // Request Payout of 100 points
        $payoutResult = $service->requestPayout($user, 100, 'bkash', '01700000000', 'Test User');
        $this->assertTrue($payoutResult['success']);

        // Assert 100 points deducted, 50 left
        $wallet->refresh();
        $this->assertEquals(50, $wallet->points_balance);

        // Admin approval test
        $payout = RewardPayoutRequest::where('user_id', $user->id)->latest()->first();
        $this->assertEquals('pending', $payout->status);
        $this->assertEquals(1000.0, $payout->amount);

        $approved = $service->approvePayout($payout);
        $this->assertTrue($approved);
        $payout->refresh();
        $this->assertEquals('approved', $payout->status);
    }
}
