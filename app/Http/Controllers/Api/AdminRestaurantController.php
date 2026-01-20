<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class AdminRestaurantController extends Controller
{
    public function index(Request $request)
    {
        $query = Restaurant::with(['country', 'restaurantType', 'user']);

        // Admin can see all restaurants including hidden ones
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $restaurants = $query->paginate($request->per_page ?? 15);

        return response()->json($restaurants);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,hidden,pending',
        ]);

        $restaurant = Restaurant::findOrFail($id);
        $restaurant->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Restaurant status updated successfully',
            'restaurant' => $restaurant,
        ]);
    }

    public function getRestaurantFoodItems($restaurantId)
    {
        $restaurant = Restaurant::with(['foodItems.foodCategory'])->findOrFail($restaurantId);

        return response()->json([
            'restaurant' => $restaurant,
            'food_items' => $restaurant->foodItems()->paginate(20),
        ]);
    }
}
