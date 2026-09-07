<?php

namespace App\Console\Commands;

use App\Models\Hotel;
use App\Services\NightAuditService;
use Illuminate\Console\Command;

class RunNightAuditCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:night-audit {--hotel-id= : Specific Hotel ID to audit} {--hotel= : Specific Hotel Code to audit} {--date= : Business Date (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute automated Night Audit for hotel properties (Room tariff posting, tax calculations, daily closure)';

    /**
     * Execute the console command.
     */
    public function handle(NightAuditService $auditService): int
    {
        $hotelId = $this->option('hotel-id');
        $hotelCode = $this->option('hotel');
        $date = $this->option('date');

        $query = Hotel::where('is_active', true);
        if ($hotelId) {
            $query->where('id', $hotelId);
        }
        if ($hotelCode) {
            $query->where('code', $hotelCode);
        }

        $hotels = $query->get();

        if ($hotels->isEmpty()) {
            $this->warn('No active hotels found for Night Audit.');
            return self::SUCCESS;
        }

        $this->info("🚀 Commencing PMS Night Audit for " . $hotels->count() . " hotel properties...");

        foreach ($hotels as $hotel) {
            $this->line("--------------------------------------------------");
            $this->info("Auditing Property: {$hotel->name} [Code: {$hotel->code}]");

            $summary = $auditService->runAudit($hotel, $date);

            $this->table(
                ['Metric', 'Value'],
                [
                    ['Business Date', $summary['audit_date']],
                    ['In-House Occupied Rooms', $summary['in_house_count']],
                    ['Tariff Charges Posted', $summary['room_charges_posted']],
                    ['Total Tax Posted (BDT)', number_format($summary['total_tax_posted'], 2)],
                    ['Total Service Charge (BDT)', number_format($summary['total_service_charge_posted'], 2)],
                    ['Overdue Checkouts', $summary['overdue_reservations']],
                    ['Completed At', $summary['completed_at']],
                ]
            );
        }

        $this->info("✅ Night Audit completed successfully for all properties.");
        return self::SUCCESS;
    }
}
