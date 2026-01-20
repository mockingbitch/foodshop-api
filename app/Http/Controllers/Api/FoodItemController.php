<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class FoodItemController extends Controller
{
    public function index(Request $request)
    {
        $query = FoodItem::with(['restaurant', 'foodCategory'])
            ->active()
            ->confirmedCode();

        // Filter by restaurant
        if ($request->has('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('food_category_id', $request->category_id);
        }

        // Filter by best seller
        if ($request->boolean('best_seller')) {
            $query->bestSeller();
        }

        // Filter by vegetarian
        if ($request->boolean('vegetarian')) {
            $query->vegetarian();
        }

        // Search by name or food code
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.vn') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.kr') LIKE ?", ["%{$search}%"])
                    ->orWhere('food_code', 'LIKE', "%{$search}%");
            });
        }

        $foodItems = $query->paginate($request->per_page ?? 15);

        return response()->json($foodItems);
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }

    public function getByCategory($categoryId)
    {
        $foodItems = FoodItem::with(['restaurant', 'foodCategory'])
            ->active()
            ->confirmedCode()
            ->where('food_category_id', $categoryId)
            ->paginate(15);

        return response()->json($foodItems);
    }

    public function getBestSeller(Request $request)
    {
        $query = FoodItem::with(['restaurant', 'foodCategory'])
            ->active()
            ->confirmedCode()
            ->bestSeller();

        if ($request->has('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        $foodItems = $query->paginate($request->per_page ?? 15);

        return response()->json($foodItems);
    }

    public function show($id)
    {
        $foodItem = FoodItem::with(['restaurant', 'foodCategory', 'reviews'])
            ->findOrFail($id);

        // Get related products (same category)
        $relatedProducts = FoodItem::with(['restaurant'])
            ->active()
            ->confirmedCode()
            ->where('food_category_id', $foodItem->food_category_id)
            ->where('id', '!=', $foodItem->id)
            ->limit(6)
            ->get();

        return response()->json([
            'food_item' => $foodItem,
            'extra_images' => $foodItem->getExtraImages(),
            'related_products' => $relatedProducts,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'food_category_id' => 'required|exists:food_categories,id',
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'description' => 'nullable|array',
            'main_image' => 'required|string',
            'extra_images' => 'nullable|array|max:5',
            'price' => 'required|numeric|min:0',
            'currency_code' => 'required|string|max:5',
            'serving_size' => 'nullable|integer',
            'weight' => 'nullable|integer',
            'is_vegetarian' => 'boolean',
        ]);

        // Check if user owns the restaurant
        $restaurant = Restaurant::findOrFail($request->restaurant_id);
        if ($restaurant->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Generate food code
        $foodCode = $this->generateFoodCode(
            $restaurant->country->code,
            $restaurant->code,
            $request->food_category_id
        );

        // Convert price to USD if not already USD
        $priceUsd = $this->convertToUSD($request->price, $request->currency_code);

        $foodItem = FoodItem::create(array_merge($request->all(), [
            'food_code' => $foodCode,
            'food_code_status' => 'pending', // Needs manager confirmation
            'price_usd' => $priceUsd,
            'status' => 'pending',
        ]));

        return response()->json([
            'message' => 'Food item created successfully. Awaiting code confirmation.',
            'food_item' => $foodItem->load(['restaurant', 'foodCategory']),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $foodItem = FoodItem::findOrFail($id);

        // Check if user owns this food item's restaurant
        if ($foodItem->restaurant->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|array',
            'description' => 'nullable|array',
            'main_image' => 'sometimes|string',
            'extra_images' => 'nullable|array|max:5',
            'price' => 'sometimes|numeric|min:0',
            'serving_size' => 'nullable|integer',
            'weight' => 'nullable|integer',
            'is_vegetarian' => 'boolean',
            'is_best_seller' => 'boolean',
        ]);

        // Recalculate USD price if price changed
        if ($request->has('price') && $request->has('currency_code')) {
            $priceUsd = $this->convertToUSD($request->price, $request->currency_code);
            $foodItem->price_usd = $priceUsd;
        }

        $foodItem->update($request->except(['food_code', 'food_code_status']));

        return response()->json([
            'message' => 'Food item updated successfully',
            'food_item' => $foodItem->load(['restaurant', 'foodCategory']),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $foodItem = FoodItem::findOrFail($id);

        // Check if user owns this food item's restaurant
        if ($foodItem->restaurant->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $foodItem->delete();

        return response()->json([
            'message' => 'Food item deleted successfully',
        ]);
    }

    public function confirmFoodCode(Request $request, $id)
    {
        $foodItem = FoodItem::findOrFail($id);

        $foodItem->update([
            'food_code_status' => 'confirmed',
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Food code confirmed successfully',
            'food_item' => $foodItem,
        ]);
    }

    public function getPendingFoodCodes()
    {
        $foodItems = FoodItem::with(['restaurant', 'foodCategory'])
            ->pendingCodeConfirmation()
            ->paginate(20);

        return response()->json($foodItems);
    }

    private function generateFoodCode($countryCode, $restaurantCode, $categoryId)
    {
        $category = \App\Models\FoodCategory::find($categoryId);
        $categoryCode = str_pad($category->code, 4, '0', STR_PAD_LEFT);

        // Get the last food item code
        $lastFoodItem = FoodItem::where('food_code', 'LIKE', "{$countryCode}-{$restaurantCode}-{$categoryCode}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastFoodItem) {
            $lastNumber = (int) substr($lastFoodItem->food_code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s-%s-%04d', $countryCode, $restaurantCode, $categoryCode, $newNumber);
    }

    private function convertToUSD($amount, $currencyCode)
    {
        if ($currencyCode === 'USD') {
            return $amount;
        }

        // Get exchange rate from database
        $exchangeRate = \App\Models\ExchangeRate::getRate($currencyCode, 'USD');

        return $exchangeRate ? $amount * $exchangeRate : $amount;
    }
}
