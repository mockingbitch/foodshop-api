<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin user
        User::updateOrCreate(
            ['email' => 'admin@foodshop.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Create test restaurant owner
        User::updateOrCreate(
            ['email' => 'owner@foodshop.com'],
            [
                'name' => 'Test Restaurant Owner',
                'password' => Hash::make('owner123'),
                'role' => 'restaurant_owner',
                'phone' => '+84123456789',
                'is_active' => true,
            ]
        );
    }
}
