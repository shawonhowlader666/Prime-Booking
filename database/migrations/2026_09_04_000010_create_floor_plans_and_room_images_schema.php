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
        Schema::create('floor_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('floor_name');
            $table->integer('floor_number')->default(1);
            $table->integer('number_of_rooms')->default(0);
            $table->string('start_room_number')->nullable();
            $table->timestamps();

            $table->index(['hotel_id', 'floor_number']);
            $table->index(['hotel_id', 'created_at']);
        });

        Schema::create('room_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('room_type_id')->nullable()->constrained('room_types')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->string('image_url');
            $table->string('caption')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['hotel_id', 'room_type_id']);
            $table->index(['hotel_id', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_images');
        Schema::dropIfExists('floor_plans');
    }
};
