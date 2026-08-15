<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hero_slides')) {
            Schema::create('hero_slides', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('badge_text')->nullable();
                $table->string('image_path')->nullable();
                $table->integer('sort_order')->default(0);
                $table->string('status', 20)->default('active');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
