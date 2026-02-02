<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\FoodItem\IndexFoodItemsRequest;
use App\Http\Requests\FoodItem\StoreFoodItemRequest;
use App\Http\Requests\FoodItem\UpdateFoodItemRequest;
use App\Services\FoodItemService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Food item CRUD, search, by category, best seller. Owner creates/updates; admin confirms food code.
 *
 * @group Food Items
 */
class FoodItemController extends BaseApiController
{
    public function __construct(
        protected FoodItemService $foodItemService
    ) {}

    /**
     * Get List of Food Items (paginated, filters: restaurant_id, category_id, best_seller, vegetarian, search, per_page)
     */
    public function index(IndexFoodItemsRequest $request): JsonResponse
    {
        $foodItems = $this->foodItemService->index($request->filters());

        return $this->success($foodItems);
    }

    /**
     * Search food items (delegates to index)
     */
    public function search(IndexFoodItemsRequest $request): JsonResponse
    {
        return $this->index($request);
    }

    /**
     * Get food items by category ID
     */
    public function getByCategory(int $categoryId): JsonResponse
    {
        $foodItems = $this->foodItemService->getByCategory($categoryId);

        return $this->success($foodItems);
    }

    /**
     * Get best seller food items (optional restaurant_id filter)
     */
    public function getBestSeller(Request $request): JsonResponse
    {
        $foodItems = $this->foodItemService->getBestSeller($request->only(['restaurant_id', 'per_page']));

        return $this->success($foodItems);
    }

    /**
     * Get food item details with related products and extra images
     */
    public function show(int $id): JsonResponse
    {
        $data = $this->foodItemService->show($id);

        return $this->success($data);
    }

    /**
     * Create food item (owner). Food code pending until admin confirmation.
     */
    public function store(StoreFoodItemRequest $request): JsonResponse
    {
        $foodItem = $this->foodItemService->store($request->user(), $request->validated());

        return $this->created(['food_item' => $foodItem], 'Food item created successfully. Awaiting code confirmation.');
    }

    /**
     * Update food item (owner or admin).
     */
    public function update(UpdateFoodItemRequest $request, int $id): JsonResponse
    {
        $foodItem = $this->foodItemService->update($request->user(), $id, $request->validated());

        return $this->success(['food_item' => $foodItem], 'Food item updated successfully');
    }

    /**
     * Delete food item (owner or admin).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->foodItemService->destroy($request->user(), $id);

        return $this->success(null, 'Food item deleted successfully');
    }

    /**
     * Confirm food code and set status active (admin).
     */
    public function confirmFoodCode(Request $request, int $id): JsonResponse
    {
        $foodItem = $this->foodItemService->confirmFoodCode($id);

        return $this->success(['food_item' => $foodItem], 'Food code confirmed successfully');
    }

    /**
     * Get food items with pending code confirmation (admin).
     */
    public function getPendingFoodCodes(): JsonResponse
    {
        $foodItems = $this->foodItemService->getPendingFoodCodes();

        return $this->success($foodItems);
    }
}
