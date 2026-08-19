<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\SearchLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * LogSearchJob — Asynchronously persists a search event to search_logs.
 *
 * Dispatched after every autocomplete selection & search form submit.
 * Queue: "low" priority → never blocks the HTTP response.
 *
 * Dedup strategy: skip if same session+query was logged in last 60 seconds
 * to avoid logging every keystroke.
 */
class LogSearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 5;

    public function __construct(
        private readonly array $payload
    ) {
        $this->onQueue('low');
    }

    public function handle(): void
    {
        try {
            $query      = trim((string) ($this->payload['query'] ?? ''));
            $sessionId  = (string) ($this->payload['session_id'] ?? '');

            if (strlen($query) < 2) {
                return; // Skip single-char noise
            }

            // ─── Dedup: same session+query within 60s → skip ─────────────
            $alreadyLogged = SearchLog::where('session_id', $sessionId)
                ->where('query', $query)
                ->where('created_at', '>=', now()->subSeconds(60))
                ->exists();

            if ($alreadyLogged) {
                return;
            }

            SearchLog::create([
                'query'          => $query,
                'resolved_city'  => $this->payload['resolved_city'] ?? null,
                'check_in'       => $this->payload['check_in']      ?? null,
                'check_out'      => $this->payload['check_out']     ?? null,
                'guests'         => (int) ($this->payload['guests'] ?? 1),
                'rooms'          => (int) ($this->payload['rooms']  ?? 1),
                'result_count'   => (int) ($this->payload['result_count'] ?? 0),
                'search_type'    => $this->payload['search_type']   ?? 'hotel',
                'user_id'        => $this->payload['user_id']       ?? null,
                'ip'             => $this->payload['ip']            ?? null,
                'session_id'     => $sessionId,
            ]);
        } catch (\Throwable $e) {
            // Silent fail — logging must NEVER crash the app
            Log::warning('LogSearchJob failed silently', [
                'error'   => $e->getMessage(),
                'payload' => $this->payload,
            ]);
        }
    }
}
