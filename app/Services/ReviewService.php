<?php

namespace App\Services;

use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\Models\FoodItem;
use App\Models\Review;
use App\Models\Restaurant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

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
        $review = $this->reviewRepository->createForReviewable(
            FoodItem::class,
            $foodItemId,
            array_merge($data, ['status' => 'pending'])
        );
        Log::info('Review created for food item', ['review_id' => $review->id, 'food_item_id' => $foodItemId]);
        return $review;
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
        $review = $this->reviewRepository->createForReviewable(
            Restaurant::class,
            $restaurantId,
            array_merge($data, ['status' => 'pending'])
        );
        Log::info('Review created for restaurant', ['review_id' => $review->id, 'restaurant_id' => $restaurantId]);
        return $review;
    }

    /**
     * Paginated list with filters (Admin).
     *
     * @param array $filters status, reviewable_type, restaurant_id, food_item_id, per_page
     * @return LengthAwarePaginator
     */
    public function index(array $filters): LengthAwarePaginator
    {
        return $this->reviewRepository->indexWithFilters($filters);
    }

    /**
     * Get review by ID (Admin).
     *
     * @return Review
     */
    public function show(int $id): Review
    {
        return $this->reviewRepository->findOrFail($id);
    }

    /**
     * Update review status (Admin): approved, pending, rejected.
     *
     * @return Review
     */
    public function updateStatus(int $id, string $status): Review
    {
        $review = $this->reviewRepository->update($id, ['status' => $status]);
        Log::info('Review status updated', ['review_id' => $id, 'status' => $status]);
        return $review;
    }

    /**
     * Delete review (Admin).
     */
    public function destroy(int $id): void
    {
        $this->reviewRepository->delete($id);
        Log::info('Review deleted', ['review_id' => $id]);
    }
}
