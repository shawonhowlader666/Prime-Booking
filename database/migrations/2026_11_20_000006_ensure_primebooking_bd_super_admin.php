<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $admin = User::where('email', 'primebooking.bd@gmail.com')->first();

        if ($admin) {
            $admin->update([
                'role'              => 'super_admin',
                'status'            => 'active',
                'email_verified_at' => $admin->email_verified_at ?? now(),
            ]);
        } else {
            User::create([
                'name'              => 'Prime Booking Super Admin',
                'email'             => 'primebooking.bd@gmail.com',
                'password'          => Hash::make('password'),
                'role'              => 'super_admin',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe keep admin role
    }
};
