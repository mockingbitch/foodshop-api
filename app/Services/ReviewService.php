<?php

namespace App\Services;

use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\Models\FoodItem;
use App\Models\Review;
use App\Models\Restaurant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Review business logic: get reviews for food item or restaurant; create review (status pending).
 */
class ReviewService
{
    public function __construct(
        protected ReviewRepositoryInterface $reviewRepository
    ) {}

    /**
     * Paginated approved reviews for a food item.
     *
     * @param int $foodItemId
     * @return LengthAwarePaginator
     */
    public function getReviews(int $foodItemId): LengthAwarePaginator
    {
        return $this->reviewRepository->getApprovedByFoodItem($foodItemId, 10);
    }

    /**
     * Create review for food item. Status pending until admin approval.
     *
     * @param int $foodItemId
     * @param array $data reviewer_name, reviewer_email?, rating, comment?, images?
     * @return Review
     */
    public function storeReview(int $foodItemId, array $data): Review
    {
        return $this->reviewRepository->createForReviewable(
            FoodItem::class,
            $foodItemId,
            array_merge($data, ['status' => 'pending'])
        );
    }

    /**
     * Paginated approved reviews for a restaurant.
     *
     * @param int $restaurantId
     * @return LengthAwarePaginator
     */
    public function getRestaurantReviews(int $restaurantId): LengthAwarePaginator
    {
        return $this->reviewRepository->getApprovedByRestaurant($restaurantId, 10);
    }

    /**
     * Create review for restaurant. Status pending until admin approval.
     *
     * @param int $restaurantId
     * @param array $data reviewer_name, reviewer_email?, rating, comment?, images?
     * @return Review
     */
    public function storeRestaurantReview(int $restaurantId, array $data): Review
    {
        return $this->reviewRepository->createForReviewable(
            Restaurant::class,
            $restaurantId,
            array_merge($data, ['status' => 'pending'])
        );
    }
}
