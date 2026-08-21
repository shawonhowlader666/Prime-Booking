<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ReEncryptGatewayKeys extends Command
{
    protected $signature   = 'gateways:reencrypt';
    protected $description = 'Safely encrypt any plaintext gateway API keys at rest (idempotent)';

    public function handle(): int
    {
        // Bypass Eloquent encrypted cast — read raw DB values directly
        $rows = DB::table('payment_gateways')->get(['id', 'gateway_code', 'name', 'api_key', 'api_secret']);

        if ($rows->isEmpty()) {
            $this->info('No payment gateways found in database.');
            return self::SUCCESS;
        }

        $this->info("Found {$rows->count()} gateway(s). Checking encryption state...");
        $this->newLine();

        foreach ($rows as $row) {
            $updates = [];

            foreach (['api_key', 'api_secret'] as $field) {
                $value = $row->$field;
                if (empty($value)) {
                    continue;
                }
                // Try to decrypt — if it succeeds, it's already encrypted
                $alreadyEncrypted = false;
                try {
                    Crypt::decrypt($value);
                    $alreadyEncrypted = true;
                } catch (\Throwable $e) {
                    $alreadyEncrypted = false;
                }

                if (! $alreadyEncrypted) {
                    $updates[$field] = Crypt::encrypt($value);
                }
            }

            if (! empty($updates)) {
                DB::table('payment_gateways')->where('id', $row->id)->update($updates);
                $this->info("  [ENCRYPTED] [{$row->gateway_code}] {$row->name}");
            } else {
                $this->line("  [SKIPPED]   [{$row->gateway_code}] {$row->name} — already encrypted");
            }
        }

        $this->newLine();
        $this->info('All gateway credentials are now encrypted at rest.');
        return self::SUCCESS;
    }
}
