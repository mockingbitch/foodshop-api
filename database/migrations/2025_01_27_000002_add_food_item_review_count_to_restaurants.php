<?php

use App\Models\FoodItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->unsignedInteger('food_item_review_count')->default(0)->after('review_count');
        });

        $this->backfillFoodItemReviewCount();
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('food_item_review_count');
        });
    }

    private function backfillFoodItemReviewCount(): void
    {
        $driver = DB::getDriverName();
        $foodItemClass = FoodItem::class;

        if ($driver === 'pgsql') {
            DB::statement("
                UPDATE restaurants r
                SET food_item_review_count = COALESCE(c.cnt, 0)
                FROM (
                    SELECT fi.restaurant_id, COUNT(reviews.id)::int AS cnt
                    FROM reviews
                    JOIN food_items fi ON fi.id = reviews.reviewable_id AND reviews.reviewable_type = ?
                    GROUP BY fi.restaurant_id
                ) c
                WHERE c.restaurant_id = r.id
            ", [$foodItemClass]);
        } else {
            DB::statement("
                UPDATE restaurants r
                INNER JOIN (
                    SELECT fi.restaurant_id, COUNT(reviews.id) AS cnt
                    FROM reviews
                    JOIN food_items fi ON fi.id = reviews.reviewable_id AND reviews.reviewable_type = ?
                    GROUP BY fi.restaurant_id
                ) c ON c.restaurant_id = r.id
                SET r.food_item_review_count = c.cnt
            ", [$foodItemClass]);
        }
    }
};
