<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();       // e.g. 'vip_silver_threshold'
            $table->string('group')->default('general'); // 'vip', 'booking', 'general', 'payment'
            $table->text('value')->nullable();     // JSON or plain text
            $table->string('type')->default('text'); // text, number, boolean, json, color
            $table->string('label');               // Human-readable label
            $table->string('description')->nullable();
            $table->boolean('is_public')->default(false); // exposed to frontend?
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
