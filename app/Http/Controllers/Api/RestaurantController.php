<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $query = Restaurant::with(['country', 'restaurantType', 'user'])
            ->active();

        // Filter by country
        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        // Filter by restaurant type
        if ($request->has('restaurant_type_id')) {
            $query->where('restaurant_type_id', $request->restaurant_type_id);
        }

        // Filter by delivery available
        if ($request->has('delivery_available')) {
            $query->where('delivery_available', $request->boolean('delivery_available'));
        }

        // Search by name or city
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.vn') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.kr') LIKE ?", ["%{$search}%"])
                    ->orWhere('city', 'LIKE', "%{$search}%");
            });
        }

        $restaurants = $query->paginate($request->per_page ?? 15);

        return response()->json($restaurants);
    }

    public function search(Request $request)
    {
        $query = Restaurant::with(['country', 'restaurantType'])->active();

        if ($request->has('name')) {
            $name = $request->name;
            $query->where(function ($q) use ($name) {
                $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$name}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.vn') LIKE ?", ["%{$name}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.kr') LIKE ?", ["%{$name}%"]);
            });
        }

        $restaurants = $query->paginate($request->per_page ?? 15);

        return response()->json($restaurants);
    }

    public function getNearby(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:100',
        ]);

        $radius = $request->radius ?? 10; // Default 10km

        $restaurants = Restaurant::with(['country', 'restaurantType'])
            ->active()
            ->nearby($request->latitude, $request->longitude, $radius)
            ->get();

        return response()->json($restaurants);
    }

    public function show($id)
    {
        $restaurant = Restaurant::with(['country', 'restaurantType', 'user', 'menus', 'reviews'])
            ->findOrFail($id);

        // Get best seller food items
        $bestSellers = $restaurant->foodItems()
            ->active()
            ->bestSeller()
            ->limit(10)
            ->get();

        return response()->json([
            'restaurant' => $restaurant,
            'best_sellers' => $bestSellers,
            'outside_images' => $restaurant->getOutsideImages(),
            'inside_images' => $restaurant->getInsideImages(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'restaurant_type_id' => 'required|exists:restaurant_types,id',
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'description' => 'nullable|array',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'zalo' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'delivery_available' => 'boolean',
            'remark' => 'nullable|array',
        ]);

        // Generate unique restaurant code
        $code = $this->generateRestaurantCode($request->country_id);

        $restaurant = Restaurant::create(array_merge($request->all(), [
            'code' => $code,
            'user_id' => $request->user()->id,
            'status' => 'pending', // Admin approval required
        ]));

        return response()->json([
            'message' => 'Restaurant created successfully. Awaiting admin approval.',
            'restaurant' => $restaurant->load(['country', 'restaurantType']),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        // Check if user owns this restaurant
        if ($restaurant->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|array',
            'description' => 'nullable|array',
            'city' => 'sometimes|string|max:100',
            'address' => 'sometimes|string',
            'phone' => 'sometimes|string|max:20',
            'zalo' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'delivery_available' => 'boolean',
            'remark' => 'nullable|array',
        ]);

        $restaurant->update($request->all());

        return response()->json([
            'message' => 'Restaurant updated successfully',
            'restaurant' => $restaurant->load(['country', 'restaurantType']),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        // Check if user owns this restaurant
        if ($restaurant->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $restaurant->delete();

        return response()->json([
            'message' => 'Restaurant deleted successfully',
        ]);
    }

    private function generateRestaurantCode($countryId)
    {
        $country = \App\Models\Country::find($countryId);
        $countryCode = $country->code;

        // Get the last restaurant code for this country
        $lastRestaurant = Restaurant::where('code', 'LIKE', "{$countryCode}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRestaurant) {
            $lastNumber = (int) substr($lastRestaurant->code, strlen($countryCode) + 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%04d', $countryCode, $newNumber);
    }
}
