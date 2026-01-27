<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Restaurant;
use App\Models\FoodItem;
use Illuminate\Http\Request;

/**
 * @group Endpoints
 */
class ReviewController extends Controller
{
    /**
     * GET api/food-items/{foodItemId}/reviews
     * 
     * Get reviews for a food item
     * 
     * @urlParam foodItemId integer required The ID of the food item. Example: 17
     */
    public function getReviews($foodItemId)
    {
        $reviews = Review::where('reviewable_type', FoodItem::class)
            ->where('reviewable_id', $foodItemId)
            ->approved()
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }

    public function storeReview(Request $request, $foodItemId)
    {
        $request->validate([
            'reviewer_name' => 'required|string|max:100',
            'reviewer_email' => 'nullable|email|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'images' => 'nullable|array|max:5',
        ]);

        $foodItem = FoodItem::findOrFail($foodItemId);

        $review = $foodItem->reviews()->create([
            'reviewer_name' => $request->reviewer_name,
            'reviewer_email' => $request->reviewer_email,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'images' => $request->images,
            'status' => 'pending', // Requires admin approval
        ]);

        return response()->json([
            'message' => 'Review submitted successfully. Awaiting approval.',
            'review' => $review,
        ], 201);
    }

    /**
     * GET api/restaurants/{restaurantId}/reviews
     * 
     * Get reviews for a restaurant
     * 
     * @urlParam restaurantId integer required The ID of the restaurant. Example: 17
     */
    public function getRestaurantReviews($restaurantId)
    {
        $reviews = Review::where('reviewable_type', Restaurant::class)
            ->where('reviewable_id', $restaurantId)
            ->approved()
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }

    public function storeRestaurantReview(Request $request, $restaurantId)
    {
        $request->validate([
            'reviewer_name' => 'required|string|max:100',
            'reviewer_email' => 'nullable|email|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'images' => 'nullable|array|max:5',
        ]);

        $restaurant = Restaurant::findOrFail($restaurantId);

        $review = $restaurant->reviews()->create([
            'reviewer_name' => $request->reviewer_name,
            'reviewer_email' => $request->reviewer_email,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'images' => $request->images,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Review submitted successfully. Awaiting approval.',
            'review' => $review,
        ], 201);
    }
}
