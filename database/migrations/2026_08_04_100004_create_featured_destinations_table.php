<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('featured_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->string('country')->default('Bangladesh');
            $table->string('image_url');                          // Hero image for the destination card
            $table->string('description')->nullable();            // Short tagline
            $table->integer('property_count_override')->nullable(); // Override auto-count if desired
            $table->decimal('min_price_override', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);       // Show on homepage hero
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('featured_destinations');
    }
};
