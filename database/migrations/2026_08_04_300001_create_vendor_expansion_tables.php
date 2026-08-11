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
        // 1. Room Availability & Custom Seasonal Pricing per Date
        Schema::create('room_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->date('date');
            $table->decimal('price', 12, 2)->nullable()->comment('Override default room price for this date');
            $table->integer('available_qty')->default(1);
            $table->boolean('is_blocked')->default(false);
            $table->timestamps();

            $table->unique(['room_id', 'date']);
        });

        // 2. Vendor Payout Requests
        Schema::create('payout_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->default('bank')->comment('bkash, nagad, bank');
            $table->text('account_details')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'processed'])->default('pending');
            $table->string('transaction_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // 3. Review Replies (Vendor response to guest reviews)
        Schema::create('review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('reply_text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_replies');
        Schema::dropIfExists('payout_requests');
        Schema::dropIfExists('room_availabilities');
    }
};
