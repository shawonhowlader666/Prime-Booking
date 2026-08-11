<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['customer','vendor','admin','super_admin'])
                      ->default('customer')->after('email');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active','inactive','banned'])
                      ->default('active')->after('role');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country', 50)->nullable()->after('avatar');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city', 80)->nullable()->after('country');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('users', 'total_bookings')) {
                $table->unsignedInteger('total_bookings')->default(0)->after('last_login_ip');
            }
            if (!Schema::hasColumn('users', 'total_spent')) {
                $table->decimal('total_spent', 12, 2)->default(0)->after('total_bookings');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['role','status','phone','avatar','country','city',
                     'last_login_at','last_login_ip','total_bookings','total_spent'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
