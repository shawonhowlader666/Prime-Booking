<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique()->index();
            $table->foreignId('package_id')->constrained('tour_packages')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('guest_name');
            $table->string('guest_email')->index();
            $table->string('guest_phone')->index();
            $table->date('travel_date');
            $table->integer('guests')->default(1);
            $table->decimal('total_amount', 12, 2);
            $table->string('payment_method')->default('pay_on_arrival');
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('confirmed');
            $table->text('special_requests')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_bookings');
    }
};
