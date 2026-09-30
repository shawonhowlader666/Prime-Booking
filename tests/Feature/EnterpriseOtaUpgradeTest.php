<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Room;
use App\Services\ICalSyncService;
use App\Services\InventoryService;
use App\Services\SocialProofService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

class EnterpriseOtaUpgradeTest extends TestCase
{
    /**
     * Test SocialProofService delivers dynamic urgency and social proof indicators.
     */
    public function test_social_proof_service_indicators(): void
    {
        $service = app(SocialProofService::class);

        // 1. Active Viewers
        $viewers = $service->getActiveViewers(101);
        $this->assertIsInt($viewers);
        $this->assertGreaterThanOrEqual(2, $viewers);
        $this->assertLessThanOrEqual(14, $viewers);

        // 2. Urgency Badge for low inventory
        $room = (new Room())->forceFill(['id' => 1, 'name' => 'Deluxe Suite']);
        
        $criticalBadge = $service->getRoomUrgencyBadge($room, 1);
        $this->assertNotNull($criticalBadge);
        $this->assertEquals('critical', $criticalBadge['type']);
        $this->assertStringContainsString('Only 1 room left', $criticalBadge['text']);

        $warningBadge = $service->getRoomUrgencyBadge($room, 3);
        $this->assertNotNull($warningBadge);
        $this->assertEquals('warning', $warningBadge['type']);
        $this->assertStringContainsString('Only 3 rooms left', $warningBadge['text']);

        $normalBadge = $service->getRoomUrgencyBadge($room, 10);
        $this->assertNull($normalBadge);

        // 3. Free Cancellation Badge
        $futureDate = now()->addDays(5)->format('Y-m-d');
        $cancelInfo = $service->getFreeCancellationBadge($futureDate);
        $this->assertTrue($cancelInfo['eligible']);
        $this->assertStringContainsString('FREE Cancellation', $cancelInfo['badge']);
    }

    /**
     * Test InventoryService room hold lock and release mechanism.
     */
    public function test_inventory_hold_and_release_mechanism(): void
    {
        $service = app(InventoryService::class);
        $roomId = 9999;
        $checkIn = now()->addDays(10)->format('Y-m-d');
        $checkOut = now()->addDays(12)->format('Y-m-d');
        $holdToken = 'test_hold_' . Str::random(10);

        // Place hold
        $held = $service->holdRoom($roomId, $checkIn, $checkOut, $holdToken, 15);
        $this->assertTrue($held);

        // Verify hold count in cache
        $date1 = now()->addDays(10)->format('Y-m-d');
        $count = (int) Cache::get("room_hold_count_{$roomId}_{$date1}", 0);
        $this->assertGreaterThanOrEqual(1, $count);

        // Release hold
        $service->releaseHold($holdToken);
        $countAfter = (int) Cache::get("room_hold_count_{$roomId}_{$date1}", 0);
        $this->assertEquals(0, $countAfter);
    }

    /**
     * Test ICalSyncService generates valid RFC 5545 iCalendar data.
     */
    public function test_ical_sync_service_generation(): void
    {
        $service = app(ICalSyncService::class);
        
        $property = (new Property())->forceFill(['id' => 1, 'name' => 'Grand Palace Hotel']);
        $room = (new Room())->forceFill(['id' => 1, 'name' => 'Ocean View Room', 'property_id' => 1]);
        $room->setRelation('property', $property);

        $sampleBooking = (new Booking())->forceFill([
            'id'                => 10,
            'room_id'           => 1,
            'booking_reference' => 'PRM-2026-TEST01',
            'guest_name'        => 'John Doe',
            'check_in'          => '2026-09-01',
            'check_out'         => '2026-09-05',
            'status'            => 'confirmed',
            'created_at'        => now(),
        ]);

        $ics = $service->generateIcsForRoom($room, collect([$sampleBooking]), collect());

        $this->assertStringContainsString('BEGIN:VCALENDAR', $ics);
        $this->assertStringContainsString('VERSION:2.0', $ics);
        $this->assertStringContainsString('PRODID:-//Prime Booking Enterprise OTA//Channel Manager iCal 2.0//EN', $ics);
        $this->assertStringContainsString('SUMMARY:Reserved - Prime Booking #PRM-2026-TEST01', $ics);
        $this->assertStringContainsString('END:VCALENDAR', $ics);
    }

    /**
     * Test Idempotency key prevents double submissions.
     */
    public function test_idempotency_key_deduplication(): void
    {
        $token = 'idemp_test_' . Str::random(16);
        $key = "idempotency_checkout_{$token}";

        // First attempt succeeds in obtaining lock
        $firstAcquired = Cache::add($key, 1, 30);
        $this->assertTrue($firstAcquired);

        // Immediate retry fails to acquire lock
        $secondAcquired = Cache::add($key, 1, 30);
        $this->assertFalse($secondAcquired);

        Cache::forget($key);
    }
}
