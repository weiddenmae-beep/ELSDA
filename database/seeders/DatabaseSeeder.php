<?php

namespace Database\Seeders;

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
        // Only seed if not in production, or if users table is empty
        if (!User::exists()) {
            // Create test user for development
            if (app()->environment('local')) {
                User::factory()->create([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ]);
            }

            // Create admin user
            User::updateOrCreate(
                ['email' => 'admin@elsda.com'],
                [
                    'name'      => 'admin',
                    'full_name' => 'Admin',
                    'email'     => 'admin@elsda.com',
                    'password'  => Hash::make('admin1234'),
                    'phone'     => '09000000000',
                    'address'   => 'elsda',
                    'role'      => 'admin',
                ]
            );
        }
    }
}
