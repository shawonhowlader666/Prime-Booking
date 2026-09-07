<?php

namespace App\Jobs;

use App\Models\Hotel;
use App\Models\SyncEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class DispatchPrimeBookingSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public array $backoff = [5, 15, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public SyncEvent $syncEvent,
        public Hotel $hotel
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payload = $this->syncEvent->payload;
        $secret = $this->hotel->sync_webhook_secret ?? 'default_secret';
        $jsonPayload = json_encode($payload);
        $signature = hash_hmac('sha256', $jsonPayload, $secret);

        $endpoint = config('services.prime_booking.webhook_url', 'http://127.0.0.1:8000/api/v1/pms/webhook');

        $this->syncEvent->increment('attempts');

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-PMS-Hotel-Code' => $this->hotel->code,
                    'X-PMS-Signature' => $signature,
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, [
                    'event_id' => $this->syncEvent->id,
                    'event_type' => $this->syncEvent->event_type,
                    'hotel_id' => $this->hotel->id,
                    'hotel_code' => $this->hotel->code,
                    'data' => $payload,
                    'timestamp' => now()->toIso8601String(),
                ]);

            if ($response->successful()) {
                $this->syncEvent->status = 'SUCCESS';
                $this->syncEvent->response_body = $response->body();
                $this->syncEvent->save();
            } else {
                $this->syncEvent->status = 'FAILED';
                $this->syncEvent->response_body = $response->body();
                $this->syncEvent->save();

                throw new \Exception("Sync event rejected by OTA endpoint: " . $response->body());
            }
        } catch (Throwable $e) {
            $this->syncEvent->status = 'FAILED';
            $this->syncEvent->response_body = $e->getMessage();
            $this->syncEvent->save();

            Log::error("Prime Booking Outbound Sync Job Exception [Attempt {$this->attempts()}]: " . $e->getMessage(), [
                'sync_event_id' => $this->syncEvent->id,
                'hotel_id' => $this->hotel->id,
            ]);

            throw $e;
        }
    }
}
