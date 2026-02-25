<?php

namespace Database\Seeders;

use App\Models\FoodItem;
use App\Models\Review;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    private array $comments = [
        'Great food and service!',
        'Rất ngon, sẽ quay lại.',
        '맛있어요! 추천합니다.',
        'Good portion size.',
        'Phục vụ nhanh, đồ ăn tươi.',
        'Nice atmosphere.',
        'A bit pricey but worth it.',
        'Best in town.',
        'Could improve the waiting time.',
        'Love the variety of dishes.',
        'Clean place, friendly staff.',
        'Will recommend to friends.',
    ];

    public function run(): void
    {
        $restaurants = Restaurant::all();
        $foodItems = FoodItem::all();

        if ($restaurants->isEmpty() || $foodItems->isEmpty()) {
            $this->command->warn('Run RestaurantSeeder and FoodItemSeeder first.');
            return;
        }

        // Review trực tiếp cho restaurant (review_count)
        foreach ($restaurants as $restaurant) {
            $numReviews = mt_rand(5, 80);
            for ($i = 0; $i < $numReviews; $i++) {
                Review::create([
                    'reviewable_type' => Restaurant::class,
                    'reviewable_id' => $restaurant->id,
                    'reviewer_name' => 'Guest ' . mt_rand(1000, 9999),
                    'reviewer_email' => 'guest' . mt_rand(100, 999) . '@example.com',
                    'rating' => mt_rand(1, 5),
                    'comment' => $this->comments[array_rand($this->comments)],
                    'status' => 'approved',
                ]);
            }
        }

        // Review cho food item (observer sẽ cập nhật food_item_review_count của restaurant)
        $foodItemIds = $foodItems->pluck('id')->toArray();
        $count = count($foodItemIds);
        $reviewsPerItem = 50; // tổng số review chia đều / lấy ngẫu nhiên món

        for ($i = 0; $i < $reviewsPerItem * 20; $i++) {
            $foodItemId = $foodItemIds[array_rand($foodItemIds)];
            Review::create([
                'reviewable_type' => FoodItem::class,
                'reviewable_id' => $foodItemId,
                'reviewer_name' => 'Customer ' . mt_rand(100, 9999),
                'reviewer_email' => 'customer' . mt_rand(100, 999) . '@example.com',
                'rating' => mt_rand(1, 5),
                'comment' => $this->comments[array_rand($this->comments)],
                'status' => 'approved',
            ]);
        }

        $this->syncRestaurantReviewCounts();
    }

    /**
     * Cập nhật review_count của từng restaurant theo đúng số review trực tiếp (reviewable_type = Restaurant).
     */
    private function syncRestaurantReviewCounts(): void
    {
        $driver = DB::getDriverName();
        $restaurantClass = Restaurant::class;

        if ($driver === 'pgsql') {
            DB::statement("
                UPDATE restaurants r
                SET review_count = COALESCE(c.cnt, 0)
                FROM (
                    SELECT reviewable_id AS restaurant_id, COUNT(*)::int AS cnt
                    FROM reviews
                    WHERE reviewable_type = ?
                    GROUP BY reviewable_id
                ) c
                WHERE c.restaurant_id = r.id
            ", [$restaurantClass]);
        } else {
            DB::statement("
                UPDATE restaurants r
                INNER JOIN (
                    SELECT reviewable_id AS restaurant_id, COUNT(*) AS cnt
                    FROM reviews
                    WHERE reviewable_type = ?
                    GROUP BY reviewable_id
                ) c ON c.restaurant_id = r.id
                SET r.review_count = c.cnt
            ", [$restaurantClass]);
        }

        // Restaurant không có review trực tiếp → 0 (default đã là 0, không cần update)
    }
}
