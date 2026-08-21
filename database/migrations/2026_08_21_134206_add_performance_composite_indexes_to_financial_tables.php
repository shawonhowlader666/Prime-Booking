<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['status', 'created_at', 'property_id'], 'idx_bk_status_created_prop');
            $table->index(['property_id', 'status'], 'idx_bk_prop_status');
        });

        Schema::table('payouts', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_payouts_status_created');
            $table->index(['vendor_id', 'status', 'created_at'], 'idx_payouts_vendor_status_created');
        });

        Schema::table('accounting_ledgers', function (Blueprint $table) {
            $table->index(['booking_id', 'type', 'category'], 'idx_ledger_idempotency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bk_status_created_prop');
            $table->dropIndex('idx_bk_prop_status');
        });

        Schema::table('payouts', function (Blueprint $table) {
            $table->dropIndex('idx_payouts_status_created');
            $table->dropIndex('idx_payouts_vendor_status_created');
        });

        Schema::table('accounting_ledgers', function (Blueprint $table) {
            $table->dropIndex('idx_ledger_idempotency');
        });
    }
};
