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
        $admin = User::firstOrNew(['email' => 'admin@aptimedha.com']);
        $admin->fill([
            'name' => 'AptiMedha Administrator',
            'username' => 'admin',
            'password' => Hash::make('Admin@12345'),
            'email_verified_at' => now(),
        ]);
        $admin->role = UserRole::Admin;
        $admin->save();

        $this->call([
            TaxonomySeeder::class,
            StaticPageSeeder::class,
            BlogSeeder::class,
            PlacementGuideSeeder::class,
            CurrentAffairSeeder::class,
        ]);
    }
}
