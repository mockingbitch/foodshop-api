<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Mặc định record mới: active / published thay vì pending / draft.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE restaurants ALTER COLUMN status SET DEFAULT 'active'");
            DB::statement("ALTER TABLE food_items ALTER COLUMN status SET DEFAULT 'active'");
            DB::statement("ALTER TABLE food_items ALTER COLUMN food_code_status SET DEFAULT 'confirmed'");
            DB::statement("ALTER TABLE news ALTER COLUMN status SET DEFAULT 'published'");
        } else {
            DB::statement("ALTER TABLE restaurants MODIFY COLUMN status ENUM('active', 'hidden', 'pending') DEFAULT 'active'");
            DB::statement("ALTER TABLE food_items MODIFY COLUMN status ENUM('active', 'hidden', 'pending') DEFAULT 'active'");
            DB::statement("ALTER TABLE food_items MODIFY COLUMN food_code_status ENUM('pending', 'confirmed', 'rejected') DEFAULT 'confirmed'");
            DB::statement("ALTER TABLE news MODIFY COLUMN status ENUM('published', 'draft', 'archived') DEFAULT 'published'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE restaurants ALTER COLUMN status SET DEFAULT 'pending'");
            DB::statement("ALTER TABLE food_items ALTER COLUMN status SET DEFAULT 'pending'");
            DB::statement("ALTER TABLE food_items ALTER COLUMN food_code_status SET DEFAULT 'pending'");
            DB::statement("ALTER TABLE news ALTER COLUMN status SET DEFAULT 'draft'");
        } else {
            DB::statement("ALTER TABLE restaurants MODIFY COLUMN status ENUM('active', 'hidden', 'pending') DEFAULT 'pending'");
            DB::statement("ALTER TABLE food_items MODIFY COLUMN status ENUM('active', 'hidden', 'pending') DEFAULT 'pending'");
            DB::statement("ALTER TABLE food_items MODIFY COLUMN food_code_status ENUM('pending', 'confirmed', 'rejected') DEFAULT 'pending'");
            DB::statement("ALTER TABLE news MODIFY COLUMN status ENUM('published', 'draft', 'archived') DEFAULT 'draft'");
        }
    }
};
