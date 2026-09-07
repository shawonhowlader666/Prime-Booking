<?php

namespace App\Services;

use App\Jobs\DispatchPrimeBookingSyncJob;
use App\Models\Hotel;
use App\Models\SyncEvent;

class PrimeBookingSyncService
{
    /**
     * Dispatch an Outbound Webhook to Prime Booking OTA engine asynchronously via queues.
     */
    public function dispatchSyncEvent(Hotel $hotel, string $eventType, array $payload, bool $async = true): SyncEvent
    {
        $event = SyncEvent::create([
            'hotel_id' => $hotel->id,
            'event_type' => $eventType,
            'payload' => $payload,
            'status' => 'PENDING',
            'attempts' => 0,
        ]);

        if ($async) {
            DispatchPrimeBookingSyncJob::dispatch($event, $hotel);
        } else {
            (new DispatchPrimeBookingSyncJob($event, $hotel))->handle();
        }

        return $event;
    }
}
