<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UpdateReviewStatusRequest;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Admin: list reviews (with filters), show review, update status, delete review.
 *
 * @group Admin - Reviews
 */
class AdminReviewController extends BaseApiController
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}

    /**
     * Get list of all reviews (Admin). Filters: status, reviewable_type, restaurant_id, food_item_id, per_page
     */
    public function index(Request $request): JsonResponse
    {
        $reviews = $this->reviewService->index($request->only([
            'status',
            'reviewable_type',
            'restaurant_id',
            'food_item_id',
            'per_page',
        ]));

        return $this->success($reviews);
    }

    /**
     * Get review by ID (Admin).
     */
    public function show(int $id): JsonResponse
    {
        $review = $this->reviewService->show($id);

        return $this->success(['review' => $review->load('reviewable')]);
    }

    /**
     * Update review status (approved, pending, rejected).
     */
    public function updateStatus(UpdateReviewStatusRequest $request, int $id): JsonResponse
    {
        $review = $this->reviewService->updateStatus($id, $request->validated('status'));

        return $this->success(['review' => $review], 'Review status updated successfully');
    }

    /**
     * Delete review (Admin).
     */
    public function destroy(int $id): JsonResponse
    {
        $this->reviewService->destroy($id);

        return $this->success(null, 'Review deleted successfully');
    }
}
