<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UpdateStatusRequest;
use App\Services\RestaurantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Admin: list all restaurants (with status filter), update restaurant status, list restaurant food items.
 *
 * @group Admin
 */
class AdminRestaurantController extends BaseApiController
{
    public function __construct(
        protected RestaurantService $restaurantService
    ) {}

    /**
     * Get list of all restaurants (Admin). Filters: status, per_page
     */
    public function index(Request $request): JsonResponse
    {
        $restaurants = $this->restaurantService->adminIndex($request->only(['status', 'per_page']));

        return $this->success($restaurants);
    }

    /**
     * Update restaurant status (active, hidden, pending).
     */
    public function updateStatus(UpdateStatusRequest $request, int $id): JsonResponse
    {
        $restaurant = $this->restaurantService->updateStatus($id, $request->validated('status'));

        return $this->success(['restaurant' => $restaurant], 'Restaurant status updated successfully');
    }

    /**
     * Get restaurant with paginated food items (Admin).
     */
    public function getRestaurantFoodItems(int $restaurantId): JsonResponse
    {
        $data = $this->restaurantService->getRestaurantFoodItems($restaurantId);

        return $this->success([
            'restaurant' => $data['restaurant'],
            'food_items' => $data['food_items'],
        ]);
    }
}
