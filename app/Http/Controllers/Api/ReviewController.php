<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;

/**
 * Reviews for food items and restaurants. List approved; create sets status pending.
 *
 * @group Reviews
 */
class ReviewController extends BaseApiController
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}

    /**
     * Get reviews for a food item
     */
    public function getReviews(int $foodItemId): JsonResponse
    {
        $reviews = $this->reviewService->getReviews($foodItemId);

        return $this->success($reviews);
    }

    /**
     * Create review for food item (public). Status pending until admin approval.
     */
    public function storeReview(StoreReviewRequest $request, int $foodItemId): JsonResponse
    {
        $review = $this->reviewService->storeReview($foodItemId, $request->validated());

        return $this->created(['review' => $review], 'Review submitted successfully. Awaiting approval.');
    }

    /**
     * Get reviews for a restaurant
     */
    public function getRestaurantReviews(int $restaurantId): JsonResponse
    {
        $reviews = $this->reviewService->getRestaurantReviews($restaurantId);

        return $this->success($reviews);
    }

    /**
     * Create review for restaurant (public). Status pending until admin approval.
     */
    public function storeRestaurantReview(StoreReviewRequest $request, int $restaurantId): JsonResponse
    {
        $review = $this->reviewService->storeRestaurantReview($restaurantId, $request->validated());

        return $this->created(['review' => $review], 'Review submitted successfully. Awaiting approval.');
    }
}
