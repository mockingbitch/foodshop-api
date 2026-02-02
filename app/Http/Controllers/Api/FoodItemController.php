<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
class FoodItemController extends Controller
{
    public function __construct(
        protected FoodItemService $foodItemService
    ) {}

    /**
     * Get List of Food Items (paginated, filters: restaurant_id, category_id, best_seller, vegetarian, search, per_page)
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'restaurant_id', 'category_id', 'best_seller', 'vegetarian', 'search', 'per_page'
        ]);
        $filters['best_seller'] = $request->boolean('best_seller');
        $filters['vegetarian'] = $request->boolean('vegetarian');

        $foodItems = $this->foodItemService->index($filters);

        return response()->json($foodItems);
    }

    /**
     * Search food items (delegates to index)
     */
    public function search(Request $request): JsonResponse
    {
        return $this->index($request);
    }

    /**
     * Get food items by category ID
     */
    public function getByCategory(int $categoryId): JsonResponse
    {
        $foodItems = $this->foodItemService->getByCategory($categoryId);

        return response()->json($foodItems);
    }

    /**
     * Get best seller food items (optional restaurant_id filter)
     */
    public function getBestSeller(Request $request): JsonResponse
    {
        $foodItems = $this->foodItemService->getBestSeller($request->only(['restaurant_id', 'per_page']));

        return response()->json($foodItems);
    }

    /**
     * Get food item details with related products and extra images
     */
    public function show(int $id): JsonResponse
    {
        $data = $this->foodItemService->show($id);

        return response()->json($data);
    }

    /**
     * Create food item (owner). Food code pending until admin confirmation.
     */
    public function store(StoreFoodItemRequest $request): JsonResponse
    {
        $foodItem = $this->foodItemService->store($request->user(), $request->validated());

        return response()->json([
            'message' => 'Food item created successfully. Awaiting code confirmation.',
            'food_item' => $foodItem,
        ], 201);
    }

    /**
     * Update food item (owner or admin).
     */
    public function update(UpdateFoodItemRequest $request, int $id): JsonResponse
    {
        $foodItem = $this->foodItemService->update($request->user(), $id, $request->validated());

        return response()->json([
            'message' => 'Food item updated successfully',
            'food_item' => $foodItem,
        ]);
    }

    /**
     * Delete food item (owner or admin).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->foodItemService->destroy($request->user(), $id);

        return response()->json(['message' => 'Food item deleted successfully']);
    }

    /**
     * Confirm food code and set status active (admin).
     */
    public function confirmFoodCode(Request $request, int $id): JsonResponse
    {
        $foodItem = $this->foodItemService->confirmFoodCode($id);

        return response()->json([
            'message' => 'Food code confirmed successfully',
            'food_item' => $foodItem,
        ]);
    }

    /**
     * Get food items with pending code confirmation (admin).
     */
    public function getPendingFoodCodes(): JsonResponse
    {
        $foodItems = $this->foodItemService->getPendingFoodCodes();

        return response()->json($foodItems);
    }
}
