<?php

namespace App\Observers;

use App\Models\FoodItem;
use App\Models\Review;
use App\Models\Restaurant;

/**
 * Cập nhật restaurants.food_item_review_count khi thêm/xóa review của food item.
 */
class ReviewObserver
{
    public function created(Review $review): void
    {
        if ($review->reviewable_type !== FoodItem::class) {
            return;
        }
        $foodItem = FoodItem::find($review->reviewable_id);
        if ($foodItem && $foodItem->restaurant_id) {
            Restaurant::where('id', $foodItem->restaurant_id)->increment('food_item_review_count');
        }
    }

    public function deleting(Review $review): void
    {
        if ($review->reviewable_type !== FoodItem::class) {
            return;
        }
        $foodItem = FoodItem::find($review->reviewable_id);
        if ($foodItem && $foodItem->restaurant_id) {
            Restaurant::where('id', $foodItem->restaurant_id)->decrement('food_item_review_count');
        }
    }
}
