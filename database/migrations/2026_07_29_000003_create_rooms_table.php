<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('name'); // Deluxe King Suite, Standard Twin, etc.
            $table->integer('max_guests')->default(2);
            $table->integer('max_adults')->default(2);
            $table->integer('max_children')->default(1);
            $table->integer('room_size_sqm')->nullable();
            $table->string('bed_type')->default('1 King Bed');
            $table->decimal('price_per_night', 10, 2);
            $table->integer('total_rooms')->default(10);
            $table->boolean('breakfast_included')->default(false);
            $table->boolean('free_cancellation')->default(true);
            $table->json('facilities')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
