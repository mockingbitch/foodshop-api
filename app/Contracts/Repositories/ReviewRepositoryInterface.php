<?php

namespace App\Contracts\Repositories;

use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Review repository contract.
 */
interface ReviewRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Paginated approved reviews for a food item.
     *
     * @param int $foodItemId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getApprovedByFoodItem(int $foodItemId, int $perPage = 10): LengthAwarePaginator;

    /**
     * Paginated approved reviews for a restaurant.
     *
     * @param int $restaurantId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getApprovedByRestaurant(int $restaurantId, int $perPage = 10): LengthAwarePaginator;

    /**
     * Create review for morph relation (FoodItem or Restaurant).
     *
     * @param string $reviewableType
     * @param int $reviewableId
     * @param array $data
     * @return Review
     */
    public function createForReviewable(string $reviewableType, int $reviewableId, array $data): Review;

    /**
     * List with filters (Admin). Paginated unless per_page=all. Filters: status, reviewable_type, restaurant_id, food_item_id, per_page.
     *
     * @param array $filters
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function indexWithFilters(array $filters): LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;

    public function count(): int;

    public function countPending(): int;

    public function countApproved(): int;
}
