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
        // High-security password with comma (,) and symbols
        $strongPassword = Hash::make('Prime@Booking,2026');

        $adminEmails = [
            'admin@primebooking.com.bd',
            'admin.primebooking.bd@gmail.com',
            'primebooking.bd@gmail.com',
            'admin@primebooking.bd.com',
            'admin@primebooking.com',
            'admin@primeavn.com',
        ];

        foreach ($adminEmails as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update([
                    'password'          => $strongPassword,
                    'role'              => 'super_admin',
                    'status'            => 'active',
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            } else {
                User::create([
                    'name'              => 'Prime Booking Super Admin',
                    'email'             => $email,
                    'password'          => $strongPassword,
                    'role'              => 'super_admin',
                    'status'            => 'active',
                    'email_verified_at' => now(),
                ]);
            }
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
