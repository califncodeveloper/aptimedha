<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     * Note: Command-line password argument removed for security.
     * Hidden interactive password prompt is strictly enforced.
     *
     * @var string
     */
    protected $signature = 'app:create-admin
                            {--name= : Full name of the administrator}
                            {--username= : Unique username for the administrator}
                            {--email= : Email address of the administrator}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Provision a secure initial administrator account with the Admin role';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('--- Provision AptiMedha Administrator Account ---');

        $name = $this->option('name') ?: $this->ask('Administrator Full Name');
        $username = $this->option('username') ?: $this->ask('Administrator Username');
        $email = $this->option('email') ?: $this->ask('Administrator Email');

        // Always require hidden interactive password input for security
        $password = $this->secret('Administrator Password (min 8 characters)');
        $passwordConfirmation = $this->secret('Confirm Administrator Password');

        if (! $password || $password !== $passwordConfirmation) {
            $this->error('Passwords do not match or are empty. Administrator creation aborted.');
            return self::FAILURE;
        }

        $validator = Validator::make([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:60', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(8)],
        ]);

        if ($validator->fails()) {
            $this->error('Administrator account creation validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->line(" - {$error}");
            }
            return self::FAILURE;
        }

        $admin = User::create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => UserRole::Admin,
        ]);

        $this->info("Administrator '{$admin->name}' ({$admin->email}) created successfully with role '{$admin->role->value}'.");

        return self::SUCCESS;
    }
}
