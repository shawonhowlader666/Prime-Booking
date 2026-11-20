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
        $superAdminEmail = 'admin.primebooking.bd@gmail.com';
        $strongPassword  = Hash::make('Prime@Booking,2026');

        // Demote any old test accounts so there is ONLY ONE single Super Admin
        User::whereIn('email', [
            'primebooking.bd@gmail.com',
            'admin@primebooking.bd.com',
            'admin@primeaviation.com',
        ])->update(['role' => 'customer']);

        // Ensure ONLY admin.primebooking.bd@gmail.com is the master Super Admin
        $user = User::where('email', $superAdminEmail)->first();
        if ($user) {
            $user->update([
                'name'              => 'Prime Booking Super Admin',
                'password'          => $strongPassword,
                'role'              => 'super_admin',
                'status'            => 'active',
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        } else {
            User::create([
                'name'              => 'Prime Booking Super Admin',
                'email'             => $superAdminEmail,
                'password'          => $strongPassword,
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
        // Safe keep admin accounts
    }
};
