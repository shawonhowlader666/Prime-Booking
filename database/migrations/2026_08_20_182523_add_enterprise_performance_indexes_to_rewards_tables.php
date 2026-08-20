<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_rewards', function (Blueprint $table) {
            // Composite index for fast balance threshold checks and tier rankings
            $table->index(['points_balance', 'user_id'], 'idx_rewards_balance_user');
            $table->index(['total_earned_points'], 'idx_rewards_earned_points');
        });

        Schema::table('reward_transactions', function (Blueprint $table) {
            // Composite index for fast user audit ledger timeline queries
            $table->index(['user_id', 'created_at'], 'idx_tx_user_created');
            $table->index(['type', 'status'], 'idx_tx_type_status');
        });

        Schema::table('reward_payout_requests', function (Blueprint $table) {
            // Composite index for ultra-fast admin pending review queries
            $table->index(['status', 'created_at'], 'idx_payout_status_created');
            $table->index(['user_id', 'status'], 'idx_payout_user_status');
        });

        Schema::table('users', function (Blueprint $table) {
            // Index for VIP tier sorting and booking spend aggregates
            $table->index(['total_spent', 'total_bookings'], 'idx_users_vip_metrics');
        });
    }

    public function down(): void
    {
        Schema::table('user_rewards', function (Blueprint $table) {
            $table->dropIndex('idx_rewards_balance_user');
            $table->dropIndex('idx_rewards_earned_points');
        });

        Schema::table('reward_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_tx_user_created');
            $table->dropIndex('idx_tx_type_status');
        });

        Schema::table('reward_payout_requests', function (Blueprint $table) {
            $table->dropIndex('idx_payout_status_created');
            $table->dropIndex('idx_payout_user_status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_vip_metrics');
        });
    }
};
