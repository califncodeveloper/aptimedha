<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // SEC-01: Ensure primary administrator account is only provisioned on a fresh system.
        // Never overwrite an existing administrator's password, role, or account details.
        $adminExists = User::where('role', UserRole::Admin)->exists();

        if (! $adminExists) {
            $existingUser = User::where('email', 'admin@aptimedha.com')->first();
            if ($existingUser) {
                $existingUser->role = UserRole::Admin;
                $existingUser->save();
            } else {
                $admin = new User([
                    'name' => 'AptiMedha Administrator',
                    'username' => 'admin',
                    'email' => 'admin@aptimedha.com',
                    'password' => Hash::make(env('INITIAL_ADMIN_PASSWORD', 'Admin@12345')),
                    'email_verified_at' => now(),
                ]);
                $admin->role = UserRole::Admin;
                $admin->save();
            }
        }

        $this->call([
            TaxonomySeeder::class,
            StaticPageSeeder::class,
            BlogSeeder::class,
            PlacementGuideSeeder::class,
            CurrentAffairSeeder::class,
        ]);
    }
}
