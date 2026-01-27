<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodCategory;
use App\Models\FoodCategoryTranslation;
use Illuminate\Http\Request;

/**
 * @group Endpoints
 */
class FoodCategoryController extends Controller
{
    /**
     * GET api/food-categories
     * 
     * Get list of food categories
     * 
     * @queryParam root_only boolean Filter root categories only. Example: true
     * @queryParam parent_id integer Filter by parent category ID. Example: 1
     */
    public function index(Request $request)
    {
        $query = FoodCategory::with(['translations', 'parent', 'children'])
            ->active();

        // Filter by parent categories only
        if ($request->boolean('root_only')) {
            $query->rootCategories();
        }

        // Filter by parent_id
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        $categories = $query->orderBy('sort_order')->get();

        return response()->json($categories);
    }

    /**
     * GET api/food-categories/{id}
     * 
     * Get food category details by ID
     * 
     * @urlParam id integer required The ID of the food category. Example: 17
     * @queryParam lang string Language code for translation. Example: en
     */
    public function show($id, Request $request)
    {
        $category = FoodCategory::with(['translations', 'parent', 'children'])
            ->findOrFail($id);

        $languageCode = $request->get('lang', 'en');
        $translation = $category->getTranslation($languageCode);

        return response()->json([
            'category' => $category,
            'translation' => $translation,
            'images' => $category->getImages(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:food_categories,code',
            'parent_id' => 'nullable|exists:food_categories,id',
            'image_1' => 'nullable|string',
            'image_2' => 'nullable|string',
            'image_3' => 'nullable|string',
            'image_4' => 'nullable|string',
            'image_5' => 'nullable|string',
            'sort_order' => 'integer',
            'translations' => 'required|array|min:1',
            'translations.*.language_code' => 'required|string|max:5',
            'translations.*.name' => 'required|string|max:200',
            'translations.*.description' => 'nullable|string',
            'translations.*.video_link' => 'nullable|string',
        ]);

        $category = FoodCategory::create($request->except('translations'));

        // Create translations
        foreach ($request->translations as $translation) {
            $category->translations()->create($translation);
        }

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category->load('translations'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $category = FoodCategory::findOrFail($id);

        $request->validate([
            'code' => 'sometimes|string|max:10|unique:food_categories,code,' . $id,
            'parent_id' => 'nullable|exists:food_categories,id',
            'image_1' => 'nullable|string',
            'image_2' => 'nullable|string',
            'image_3' => 'nullable|string',
            'image_4' => 'nullable|string',
            'image_5' => 'nullable|string',
            'sort_order' => 'integer',
        ]);

        $category->update($request->except('translations'));

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category->load('translations'),
        ]);
    }

    public function destroy($id)
    {
        $category = FoodCategory::findOrFail($id);

        // Check if category has children
        if ($category->children()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with subcategories',
            ], 422);
        }

        // Check if category has food items
        if ($category->foodItems()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with food items',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }

    public function addTranslation(Request $request, $id)
    {
        $request->validate([
            'language_code' => 'required|string|max:5',
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'video_link' => 'nullable|string',
        ]);

        $category = FoodCategory::findOrFail($id);

        // Update or create translation
        $translation = FoodCategoryTranslation::updateOrCreate(
            [
                'food_category_id' => $id,
                'language_code' => $request->language_code,
            ],
            $request->only(['name', 'description', 'video_link'])
        );

        return response()->json([
            'message' => 'Translation added/updated successfully',
            'translation' => $translation,
        ]);
    }
}
