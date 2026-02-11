<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Truncate in reverse dependency order to avoid duplicate data on re-seed
        DB::table('food_items')->truncate();
        DB::table('restaurants')->truncate();
        DB::table('news')->truncate();
        DB::table('food_category_translations')->truncate();
        DB::table('food_categories')->truncate();
        DB::table('personal_access_tokens')->truncate();
        DB::table('users')->truncate();
        DB::table('countries')->truncate();
        DB::table('restaurant_types')->truncate();
        DB::table('languages')->truncate();

        Schema::enableForeignKeyConstraints();

        $this->call([
            LanguageSeeder::class,
            CountrySeeder::class,
            RestaurantTypeSeeder::class,
            AdminUserSeeder::class,
            FoodCategorySeeder::class,
            RestaurantSeeder::class,
            FoodItemSeeder::class,
            NewsSeeder::class,
        ]);
    }
}
