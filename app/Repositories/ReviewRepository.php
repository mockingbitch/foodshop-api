<?php

namespace App\Repositories;

use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\Models\FoodItem;
use App\Models\Review;
use App\Models\Restaurant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Review repository: Eloquent query layer for Review (polymorphic: FoodItem, Restaurant).
 * Handles get approved by reviewable, create for morph.
 */
class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }

    /**
     * Paginated approved reviews for a food item.
     *
     * @param int $foodItemId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getApprovedByFoodItem(int $foodItemId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->query()
            ->where('reviewable_type', FoodItem::class)
            ->where('reviewable_id', $foodItemId)
            ->approved()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Paginated approved reviews for a restaurant.
     *
     * @param int $restaurantId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getApprovedByRestaurant(int $restaurantId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->query()
            ->where('reviewable_type', Restaurant::class)
            ->where('reviewable_id', $restaurantId)
            ->approved()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create review for morph relation (FoodItem or Restaurant).
     *
     * @param string $reviewableType FoodItem::class or Restaurant::class
     * @param int $reviewableId
     * @param array $data reviewer_name, reviewer_email?, rating, comment?, images?, status
     * @return Review
     */
    public function createForReviewable(string $reviewableType, int $reviewableId, array $data): Review
    {
        $reviewable = $reviewableType === FoodItem::class
            ? FoodItem::findOrFail($reviewableId)
            : Restaurant::findOrFail($reviewableId);

        return $reviewable->reviews()->create($data);
    }

    /**
     * Paginated list with filters (Admin).
     *
     * @param array $filters status, reviewable_type (restaurant|food_item), restaurant_id, food_item_id, per_page
     * @return LengthAwarePaginator
     */
    public function indexWithFilters(array $filters): LengthAwarePaginator
    {
        $query = $this->query()->with('reviewable')->latest();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['reviewable_type'])) {
            $type = $filters['reviewable_type'] === 'restaurant' ? Restaurant::class : FoodItem::class;
            $query->where('reviewable_type', $type);
        }

        if (! empty($filters['restaurant_id'])) {
            $query->where('reviewable_type', Restaurant::class)->where('reviewable_id', $filters['restaurant_id']);
        }

        if (! empty($filters['food_item_id'])) {
            $query->where('reviewable_type', FoodItem::class)->where('reviewable_id', $filters['food_item_id']);
        }

        $perPage = (int) ($filters['per_page'] ?? 15);
        $perPage = min(max($perPage, 1), 100);

        return $query->paginate($perPage);
    }

    /** Count all reviews. */
    public function count(): int
    {
        return $this->query()->count();
    }

    /** Count pending reviews. */
    public function countPending(): int
    {
        return $this->query()->pending()->count();
    }

    /** Count approved reviews. */
    public function countApproved(): int
    {
        return $this->query()->approved()->count();
    }
}
