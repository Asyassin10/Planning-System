<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Route;
use App\Models\Resource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users for each team
        $teamAUser = User::create([
            'name' => 'Alice (Team A)',
            'email' => 'alice@example.com',
            'password' => Hash::make('password'),
            'role' => 'team_a',
        ]);

        $teamBUser = User::create([
            'name' => 'Bob (Team B)',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
            'role' => 'team_b',
        ]);

        $teamCUser = User::create([
            'name' => 'Charlie (Team C)',
            'email' => 'charlie@example.com',
            'password' => Hash::make('password'),
            'role' => 'team_c',
        ]);

        // Create sample routes
        Route::create([
            'name' => 'Downtown Express',
            'identifier' => 'DTE-001',
            'description' => 'Main downtown route serving business district',
        ]);

        Route::create([
            'name' => 'Airport Shuttle',
            'identifier' => 'AS-002',
            'description' => 'Direct route to international airport',
        ]);

        Route::create([
            'name' => 'Suburban Loop',
            'identifier' => 'SL-003',
            'description' => 'Circular route covering suburban areas',
        ]);

        Route::create([
            'name' => 'University Line',
            'identifier' => 'UL-004',
            'description' => 'Route connecting main university campuses',
        ]);

        Route::create([
            'name' => 'Industrial Park',
            'identifier' => 'IP-005',
            'description' => 'Service to industrial and manufacturing zones',
        ]);

        // Create sample resources - Vehicles
        Resource::create([
            'type' => 'vehicle',
            'name' => 'Bus #101',
            'details' => ['capacity' => 50, 'fuel_type' => 'diesel'],
            'is_active' => true,
        ]);

        Resource::create([
            'type' => 'vehicle',
            'name' => 'Bus #102',
            'details' => ['capacity' => 50, 'fuel_type' => 'diesel'],
            'is_active' => true,
        ]);

        Resource::create([
            'type' => 'vehicle',
            'name' => 'Bus #103',
            'details' => ['capacity' => 40, 'fuel_type' => 'electric'],
            'is_active' => true,
        ]);

        Resource::create([
            'type' => 'vehicle',
            'name' => 'Minibus #201',
            'details' => ['capacity' => 20, 'fuel_type' => 'hybrid'],
            'is_active' => true,
        ]);

        // Create sample resources - Workers
        Resource::create([
            'type' => 'worker',
            'name' => 'Driver: John Smith',
            'details' => ['license' => 'CDL-A', 'experience_years' => 10],
            'is_active' => true,
        ]);

        Resource::create([
            'type' => 'worker',
            'name' => 'Driver: Jane Doe',
            'details' => ['license' => 'CDL-B', 'experience_years' => 5],
            'is_active' => true,
        ]);

        Resource::create([
            'type' => 'worker',
            'name' => 'Driver: Mike Johnson',
            'details' => ['license' => 'CDL-A', 'experience_years' => 8],
            'is_active' => true,
        ]);

        Resource::create([
            'type' => 'worker',
            'name' => 'Supervisor: Sarah Wilson',
            'details' => ['role' => 'operations_supervisor', 'experience_years' => 15],
            'is_active' => true,
        ]);

        $this->command->info('Sample data seeded successfully!');
        $this->command->info('Users created:');
        $this->command->info('- alice@example.com (Team A) - password: password');
        $this->command->info('- bob@example.com (Team B) - password: password');
        $this->command->info('- charlie@example.com (Team C) - password: password');
    }
}
