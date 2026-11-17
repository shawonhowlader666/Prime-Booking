<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Calendar rows created by a price change or a block got available_qty = 1 (the column
     * default), which made every such date sell as if the room type had a single unit.
     * No screen ever let anyone set a per-day quantity, so for open (not blocked) days the
     * correct value is the room type's full inventory. Blocked days stay blocked.
     */
    public function up(): void
    {
        if (!Schema::hasTable('room_availabilities') || !Schema::hasTable('rooms')) {
            return;
        }

        DB::table('rooms')->select(['id', 'total_rooms'])->orderBy('id')->chunkById(500, function ($rooms) {
            foreach ($rooms as $room) {
                DB::table('room_availabilities')
                    ->where('room_id', $room->id)
                    ->where('is_blocked', false)
                    ->where('available_qty', '<', (int) $room->total_rooms)
                    ->update(['available_qty' => (int) $room->total_rooms]);
            }
        });
    }

    public function down(): void
    {
        // Data repair; the previous values were wrong and are not restored.
    }
};
