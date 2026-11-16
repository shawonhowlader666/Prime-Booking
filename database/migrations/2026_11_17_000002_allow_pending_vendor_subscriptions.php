<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Paid partner plans are requested first ("pending") and activated by an
     * administrator after payment, so the status column needs that value.
     */
    public function up(): void
    {
        if (!Schema::hasTable('vendor_subscriptions')) {
            return;
        }

        Schema::table('vendor_subscriptions', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->change();
        });
    }

    public function down(): void
    {
        // The wider string column is a superset of the old enum; nothing to undo.
    }
};
