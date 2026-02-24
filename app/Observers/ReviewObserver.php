<?php

namespace App\Observers;

use App\Models\FoodItem;
use App\Models\Review;
use App\Models\Restaurant;

/**
 * Cập nhật count khi thêm/xóa review:
 * - Review trực tiếp restaurant → review_count
 * - Review food item → food_item_review_count
 */
class ReviewObserver
{
    public function created(Review $review): void
    {
        if ($review->reviewable_type === Restaurant::class) {
            Restaurant::where('id', $review->reviewable_id)->increment('review_count');
            return;
        }
        if ($review->reviewable_type === FoodItem::class) {
            $foodItem = FoodItem::find($review->reviewable_id);
            if ($foodItem && $foodItem->restaurant_id) {
                Restaurant::where('id', $foodItem->restaurant_id)->increment('food_item_review_count');
            }
        }
    }

    public function deleting(Review $review): void
    {
        if ($review->reviewable_type === Restaurant::class) {
            Restaurant::where('id', $review->reviewable_id)->decrement('review_count');
            return;
        }
        if ($review->reviewable_type === FoodItem::class) {
            $foodItem = FoodItem::find($review->reviewable_id);
            if ($foodItem && $foodItem->restaurant_id) {
                Restaurant::where('id', $foodItem->restaurant_id)->decrement('food_item_review_count');
            }
        }
    }
}
