<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateStatusRequest;
use App\Services\RestaurantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Admin: list all restaurants (with status filter), update restaurant status, list restaurant food items.
 *
 * @group Admin
 */
class AdminRestaurantController extends Controller
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

        return response()->json($restaurants);
    }

    /**
     * Update restaurant status (active, hidden, pending).
     */
    public function updateStatus(UpdateStatusRequest $request, int $id): JsonResponse
    {
        $restaurant = $this->restaurantService->updateStatus($id, $request->validated('status'));

        return response()->json([
            'message' => 'Restaurant status updated successfully',
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * Get restaurant with paginated food items (Admin).
     */
    public function getRestaurantFoodItems(int $restaurantId): JsonResponse
    {
        $data = $this->restaurantService->getRestaurantFoodItems($restaurantId);

        return response()->json([
            'restaurant' => $data['restaurant'],
            'food_items' => $data['food_items'],
        ]);
    }
}
