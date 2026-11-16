<?php

namespace App\Console\Commands;

use App\Services\Booking\BookingLifecycleService;
use Illuminate\Console\Command;

class ExpirePendingBookings extends Command
{
    protected $signature = 'bookings:expire-pending';

    protected $description = 'Release unpaid online bookings whose payment window has closed, and prune expired checkout holds';

    public function handle(BookingLifecycleService $lifecycle): int
    {
        $released = $lifecycle->expireUnpaid();

        $this->info("Released {$released} unpaid booking(s).");

        return self::SUCCESS;
    }
}
