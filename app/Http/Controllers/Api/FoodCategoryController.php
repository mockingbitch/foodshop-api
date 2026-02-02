<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FoodCategory\AddFoodCategoryTranslationRequest;
use App\Http\Requests\FoodCategory\StoreFoodCategoryRequest;
use App\Http\Requests\FoodCategory\UpdateFoodCategoryRequest;
use App\Services\FoodCategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Food category CRUD and translations (Admin). Hierarchical categories, multilingual.
 *
 * @group Food Categories
 */
class FoodCategoryController extends Controller
{
    public function __construct(
        protected FoodCategoryService $foodCategoryService
    ) {}

    /**
     * Get list of food categories (filters: root_only, parent_id)
     */
    public function index(Request $request): JsonResponse
    {
        $categories = $this->foodCategoryService->index([
            'root_only' => $request->boolean('root_only'),
            'parent_id' => $request->parent_id,
        ]);

        return response()->json($categories);
    }

    /**
     * Get food category details by ID (query: lang for translation)
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $data = $this->foodCategoryService->show($id, $request->get('lang', 'en'));

        return response()->json($data);
    }

    /**
     * Create category with translations (Admin).
     */
    public function store(StoreFoodCategoryRequest $request): JsonResponse
    {
        $category = $this->foodCategoryService->store($request->validated());

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category,
        ], 201);
    }

    /**
     * Update category (Admin).
     */
    public function update(UpdateFoodCategoryRequest $request, int $id): JsonResponse
    {
        $category = $this->foodCategoryService->update($id, $request->validated());

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category,
        ]);
    }

    /**
     * Delete category (Admin). Returns 422 if has children or food items.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->foodCategoryService->destroy($id);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->errors()->first(),
            ], 422);
        }

        return response()->json(['message' => 'Category deleted successfully']);
    }

    /**
     * Add or update translation for category (Admin).
     */
    public function addTranslation(AddFoodCategoryTranslationRequest $request, int $id): JsonResponse
    {
        $translation = $this->foodCategoryService->addTranslation($id, $request->validated());

        return response()->json([
            'message' => 'Translation added/updated successfully',
            'translation' => $translation,
        ]);
    }
}
