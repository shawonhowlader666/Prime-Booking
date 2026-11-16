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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'trade_license')) {
                $table->string('trade_license', 100)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'payout_method')) {
                $table->string('payout_method', 50)->nullable()->after('trade_license');
            }
            if (!Schema::hasColumn('users', 'account_no')) {
                $table->string('account_no', 100)->nullable()->after('payout_method');
            }
            if (!Schema::hasColumn('users', 'account_holder')) {
                $table->string('account_holder', 150)->nullable()->after('account_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['trade_license', 'payout_method', 'account_no', 'account_holder']);
        });
    }
};
