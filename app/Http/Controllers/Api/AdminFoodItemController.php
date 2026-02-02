<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UpdateStatusRequest;
use App\Services\FoodItemService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Admin: update food item status; list food items by restaurant (with status filter).
 *
 * @group Admin
 */
class AdminFoodItemController extends BaseApiController
{
    public function __construct(
        protected FoodItemService $foodItemService
    ) {}

    /**
     * Update food item status (active, hidden, pending).
     */
    public function updateStatus(UpdateStatusRequest $request, int $id): JsonResponse
    {
        $foodItem = $this->foodItemService->updateStatus($id, $request->validated('status'));

        return $this->success(['food_item' => $foodItem], 'Food item status updated successfully');
    }

    /**
     * Get food items for a restaurant (Admin). Filter: status
     */
    public function getRestaurantFoodItems(int $restaurantId, Request $request): JsonResponse
    {
        $foodItems = $this->foodItemService->getRestaurantFoodItems(
            $restaurantId,
            $request->only(['status'])
        );

        return $this->success($foodItems);
    }
}
