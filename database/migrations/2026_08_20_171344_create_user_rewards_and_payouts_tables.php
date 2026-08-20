<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. User Rewards Wallet Ledger
        Schema::create('user_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->unsignedInteger('points_balance')->default(0);
            $table->unsignedInteger('total_earned_points')->default(0);
            $table->unsignedInteger('total_redeemed_points')->default(0);
            $table->timestamps();
        });

        // 2. Detailed Immutable Transactions Log (Audit Trail)
        Schema::create('reward_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // 'earned', 'redeemed', 'payout', 'admin_adjustment'
            $table->integer('points'); // +10 or -100
            $table->decimal('amount_value', 10, 2)->default(0.00); // monetary equivalent in BDT
            $table->string('description');
            $table->string('status')->default('completed'); // 'pending', 'completed', 'cancelled'
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // 3. Reward Cashout / Withdrawal Requests
        Schema::create('reward_payout_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('points');
            $table->decimal('amount', 10, 2);
            $table->string('payment_gateway'); // 'bkash', 'nagad', 'rocket', 'bank'
            $table->string('account_number');
            $table->string('account_name')->nullable();
            $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected'
            $table->text('admin_note')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_payout_requests');
        Schema::dropIfExists('reward_transactions');
        Schema::dropIfExists('user_rewards');
    }
};
