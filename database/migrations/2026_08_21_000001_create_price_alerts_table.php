<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('price_alerts')) {
            Schema::create('price_alerts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
                $table->string('email', 150);
                $table->decimal('target_price', 10, 2)->nullable();
                $table->decimal('current_price_at_alert', 10, 2)->default(0.00);
                $table->string('status', 30)->default('active'); // active, notified, cancelled
                $table->timestamp('last_notified_at')->nullable();
                $table->timestamps();

                $table->index(['property_id', 'status']);
                $table->index(['email', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('price_alerts');
    }
};
