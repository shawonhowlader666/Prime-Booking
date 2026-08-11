<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();

            // Content
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('badge_text')->nullable();           // "LIMITED TIME", "ESCAPE NOW"
            $table->string('cta_text')->nullable();             // "Up to 40% OFF"
            $table->string('cta_link')->nullable();             // URL or route
            $table->string('image_url')->nullable();            // background image
            $table->string('icon')->nullable();                 // emoji or icon class

            // Styling
            $table->string('bg_color')->default('#1890ff');     // gradient start color
            $table->string('bg_color_end')->nullable();         // gradient end color
            $table->string('text_color')->default('#ffffff');
            $table->string('badge_bg')->default('#f5c518');     // badge background

            // Categorization
            $table->enum('type', [
                'accommodation',    // Hotel/Resort promotions
                'flights',          // Flight promotions
                'activities',       // Activities / Things to do
                'destination',      // Featured destination
                'general',          // General/homepage banner
            ])->default('accommodation');

            $table->string('target_type')->nullable();         // "hotel","houseboat" etc for filtering
            $table->string('target_city')->nullable();         // specific city link

            // Vendor-specific promotion (null = admin global)
            $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();

            // Control
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['type', 'is_active', 'sort_order']);
            $table->index(['vendor_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
