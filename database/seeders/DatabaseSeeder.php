<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LanguageSeeder::class,
            CountrySeeder::class,
            RestaurantTypeSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
