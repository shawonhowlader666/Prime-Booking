<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'dob')) {
                $table->date('dob')->nullable();
            }
            if (! Schema::hasColumn('users', 'gender')) {
                $table->string('gender', 20)->nullable();
            }
            if (! Schema::hasColumn('users', 'passport_number')) {
                $table->string('passport_number', 100)->nullable();
            }
            if (! Schema::hasColumn('users', 'passport_expiry')) {
                $table->date('passport_expiry')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dob', 'gender', 'passport_number', 'passport_expiry']);
        });
    }
};
