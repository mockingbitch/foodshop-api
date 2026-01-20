<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use Illuminate\Http\Request;

class AdminFoodItemController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,hidden,pending',
        ]);

        $foodItem = FoodItem::findOrFail($id);
        $foodItem->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Food item status updated successfully',
            'food_item' => $foodItem,
        ]);
    }

    public function getRestaurantFoodItems($restaurantId, Request $request)
    {
        $query = FoodItem::with(['foodCategory'])
            ->where('restaurant_id', $restaurantId);

        // Admin can filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $foodItems = $query->paginate(20);

        return response()->json($foodItems);
    }
}
