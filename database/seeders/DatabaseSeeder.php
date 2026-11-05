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
        // Ensure primary administrator account exists
        User::updateOrCreate(
            ['email' => 'admin@aptimedha.com'],
            [
                'name' => 'AptiMedha Administrator',
                'username' => 'admin',
                'password' => Hash::make('Admin@12345'),
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            TaxonomySeeder::class,
            StaticPageSeeder::class,
            BlogSeeder::class,
            PlacementGuideSeeder::class,
            CurrentAffairSeeder::class,
        ]);
    }
}
