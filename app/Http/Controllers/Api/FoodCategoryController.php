<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
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
class FoodCategoryController extends BaseApiController
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

        return $this->success($categories);
    }

    /**
     * Get food category details by ID (query: lang for translation)
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $data = $this->foodCategoryService->show($id, $request->get('lang', 'en'));

        return $this->success($data);
    }

    /**
     * Create category with translations (Admin).
     */
    public function store(StoreFoodCategoryRequest $request): JsonResponse
    {
        $category = $this->foodCategoryService->store($request->validated());

        return $this->created(['category' => $category], 'Category created successfully');
    }

    /**
     * Update category (Admin).
     */
    public function update(UpdateFoodCategoryRequest $request, int $id): JsonResponse
    {
        $category = $this->foodCategoryService->update($id, $request->validated());

        return $this->success(['category' => $category], 'Category updated successfully');
    }

    /**
     * Delete category (Admin). Returns 422 if has children or food items.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->foodCategoryService->destroy($id);
        } catch (ValidationException $e) {
            return $this->validationError($e->errors(), $e->errors()->first());
        }

        return $this->success(null, 'Category deleted successfully');
    }

    /**
     * Add or update translation for category (Admin).
     */
    public function addTranslation(AddFoodCategoryTranslationRequest $request, int $id): JsonResponse
    {
        $translation = $this->foodCategoryService->addTranslation($id, $request->validated());

        return $this->success(['translation' => $translation], 'Translation added/updated successfully');
    }
}
