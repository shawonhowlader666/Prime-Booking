<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('properties')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->string('address')->nullable()->change();
                $table->string('type')->default('hotel')->change();
                $table->string('city')->nullable()->change();
                $table->text('description')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // No down needed for safety nullability
    }
};
