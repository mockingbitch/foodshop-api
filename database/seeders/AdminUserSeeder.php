<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds default admin and test owner. Credentials used by Postman collection (postman/FoodShop-API.postman_collection.json).
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin: admin@foodshop.com / admin123 (Postman: admin_email, admin_password)
        User::updateOrCreate(
            ['email' => 'admin@foodshop.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Owner: owner@foodshop.com / owner123 (Postman: owner_email, owner_password)
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
