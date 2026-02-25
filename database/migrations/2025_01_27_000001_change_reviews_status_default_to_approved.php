<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Đổi default status của reviews từ pending sang approved (review không cần duyệt).
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE reviews MODIFY COLUMN status ENUM('approved', 'pending', 'rejected') DEFAULT 'approved'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE reviews ALTER COLUMN status SET DEFAULT 'approved'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE reviews MODIFY COLUMN status ENUM('approved', 'pending', 'rejected') DEFAULT 'pending'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE reviews ALTER COLUMN status SET DEFAULT 'pending'");
        }
    }
};
