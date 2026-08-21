<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('inquiries', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('inquiries', 'property_id')) {
                $table->foreignId('property_id')->nullable()->after('user_id')->constrained('properties')->nullOnDelete();
            }
            if (!Schema::hasColumn('inquiries', 'vendor_id')) {
                $table->foreignId('vendor_id')->nullable()->after('property_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('inquiries', 'subject')) {
                $table->string('subject')->nullable()->after('email');
            }
            if (!Schema::hasColumn('inquiries', 'reply')) {
                $table->text('reply')->nullable()->after('message');
            }
            if (!Schema::hasColumn('inquiries', 'replied_at')) {
                $table->timestamp('replied_at')->nullable()->after('reply');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('property_id');
            $table->dropConstrainedForeignId('vendor_id');
            $table->dropColumn(['reply', 'replied_at']);
        });
    }
};
