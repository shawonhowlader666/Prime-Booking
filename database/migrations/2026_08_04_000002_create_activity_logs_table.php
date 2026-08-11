<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_name')->default('System');
            $table->string('action', 50);                    // created, updated, deleted, login
            $table->string('model_type', 50)->nullable();    // Property, Booking, User
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('created_at')->useCurrent();   // no updated_at

            // Indexes for activity log queries
            $table->index(['action', 'created_at'], 'idx_log_action_time');
            $table->index(['user_id', 'created_at'], 'idx_log_user_time');
            $table->index(['model_type', 'model_id'], 'idx_log_model');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
