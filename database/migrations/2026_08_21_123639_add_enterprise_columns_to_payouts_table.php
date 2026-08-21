<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payouts', function (Blueprint $table) {
            // Vendor bank/mobile account number for the payout
            if (! Schema::hasColumn('payouts', 'account_number')) {
                $table->string('account_number', 100)->nullable()->after('account_details');
            }
            // Admin who processed/approved this payout
            if (! Schema::hasColumn('payouts', 'processed_by')) {
                $table->string('processed_by', 100)->nullable()->after('account_number');
            }
            // Exact timestamp of processing
            if (! Schema::hasColumn('payouts', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('processed_by');
            }
            // Gateway fee deducted during payout transfer (bKash/Nagad charge)
            if (! Schema::hasColumn('payouts', 'fee_deducted')) {
                $table->decimal('fee_deducted', 10, 2)->default(0.00)->after('processed_at');
            }
            // Vendor's requested timestamp
            if (! Schema::hasColumn('payouts', 'requested_at')) {
                $table->timestamp('requested_at')->nullable()->after('fee_deducted');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payouts', function (Blueprint $table) {
            $table->dropColumn(['account_number', 'processed_by', 'processed_at', 'fee_deducted', 'requested_at']);
        });
    }
};

