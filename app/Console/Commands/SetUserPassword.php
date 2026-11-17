<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Create a staff account or reset its password from the server shell.
 * The password is typed (hidden) or generated; it is never stored in code or git.
 */
class SetUserPassword extends Command
{
    protected $signature = 'users:set-password
                            {email : Account e-mail address}
                            {--role= : Create / change the role (customer, vendor, admin, super_admin)}
                            {--name= : Display name when creating the account}
                            {--generate : Generate a strong random password and print it once}';

    protected $description = 'Create an account or set its password securely (no passwords in code)';

    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid e-mail address.');
            return self::FAILURE;
        }

        $role = $this->option('role');
        if ($role !== null && !in_array($role, ['customer', 'vendor', 'admin', 'super_admin'], true)) {
            $this->error('Role must be one of: customer, vendor, admin, super_admin.');
            return self::FAILURE;
        }

        if ($this->option('generate')) {
            $password = Str::password(20);
        } else {
            $password = (string) $this->secret('New password (min 12 characters)');
            if (strlen($password) < 12) {
                $this->error('Password must be at least 12 characters.');
                return self::FAILURE;
            }
            if ($password !== (string) $this->secret('Repeat password')) {
                $this->error('Passwords do not match.');
                return self::FAILURE;
            }
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name'              => $this->option('name') ?: Str::headline(Str::before($email, '@')),
                'email'             => $email,
                'password'          => Hash::make($password),
                'role'              => $role ?: 'customer',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]);
            $this->info("Created {$user->role} account {$email}.");
        } else {
            $user->forceFill(array_filter([
                'password' => Hash::make($password),
                'role'     => $role,
                'status'   => 'active',
            ]))->save();
            $user->tokens()->delete();
            $this->info("Password updated for {$email} ({$user->role}). Existing API tokens revoked.");
        }

        if ($this->option('generate')) {
            $this->warn("Generated password (shown once): {$password}");
        }

        return self::SUCCESS;
    }
}
