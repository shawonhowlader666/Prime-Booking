<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique(); // e.g. PRM-87X2-K9L
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('property_id')->nullable()->constrained('properties')->onDelete('set null');
            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('set null');
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone');
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('nights')->default(1);
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('BDT');
            $table->string('payment_status')->default('pending'); // pending, paid, refunded, failed
            $table->string('booking_status')->default('confirmed'); // confirmed, cancelled, completed
            $table->text('special_requests')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
