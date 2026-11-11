<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\CoxsBazarHotelsSeeder;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use App\Models\Room;

/**
 * ImportCoxsBazarHotelsCommand
 *
 * Senior Engineering Practice:
 * Clean CLI command with options, confirmation, and structured output.
 */
class ImportCoxsBazarHotelsCommand extends Command
{
    protected $signature = 'hotels:import-coxsbazar 
                            {--purge : Wipe old properties and rooms before importing}
                            {--force : Bypass confirmation prompts}';

    protected $description = 'Import authentic Cox\'s Bazar luxury hotels, rooms, photos, and videos into the system';

    public function handle(): int
    {
        $this->info("=== PRIME BOOKING — COX'S BAZAR HOTEL IMPORTER ===");

        if ($this->option('purge')) {
            if (!$this->option('force') && !$this->confirm('Are you sure you want to PURGE all existing properties and rooms?')) {
                $this->warn('Operation cancelled.');
                return Command::SUCCESS;
            }

            $this->warn('Purging existing properties, rooms, and linked records...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('bookings')->delete();
            DB::table('booking_addons')->delete();
            DB::table('room_availabilities')->delete();
            DB::table('rooms')->delete();
            DB::table('property_amenities')->delete();
            DB::table('wishlists')->delete();
            DB::table('reviews')->delete();
            DB::table('review_replies')->delete();
            DB::table('inquiries')->delete();
            DB::table('price_alerts')->delete();
            DB::table('properties')->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info('Database cleaned successfully.');
        }

        $this->info('Running CoxsBazarHotelsSeeder...');
        $seeder = new CoxsBazarHotelsSeeder();
        $seeder->setCommand($this);
        $seeder->run();

        $hotelCount = Property::where('city', "Cox's Bazar")->count();
        $roomCount  = Room::count();

        $this->newLine();
        $this->info(" Successfully imported {$hotelCount} hotels with {$roomCount} rooms!");
        
        $tableData = Property::where('city', "Cox's Bazar")
            ->withCount('rooms')
            ->get()
            ->map(fn($p) => [
                'ID' => $p->id,
                'Name' => $p->name,
                'Stars' => $p->star_rating . ' ★',
                'Min Price' => '৳' . number_format((float) $p->price_per_night),
                'Rooms' => $p->rooms_count,
                'Status' => $p->status,

            ]);

        $this->table(['ID', 'Name', 'Stars', 'Min Price', 'Rooms', 'Status'], $tableData);

        return Command::SUCCESS;
    }
}
