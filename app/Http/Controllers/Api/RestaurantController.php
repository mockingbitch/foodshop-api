<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Restaurant\GetNearbyRestaurantRequest;
use App\Http\Requests\Restaurant\StoreRestaurantRequest;
use App\Http\Requests\Restaurant\UpdateRestaurantRequest;
use App\Services\RestaurantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Restaurant CRUD, search, nearby (by coordinates), menus. Owner creates/updates; admin approves.
 *
 * @group Restaurants
 */
class RestaurantController extends BaseApiController
{
    public function __construct(
        protected RestaurantService $restaurantService
    ) {}

    /**
     * Get List of Restaurants (paginated, filters: country_id, restaurant_type_id, delivery_available, search, per_page)
     */
    public function index(Request $request): JsonResponse
    {
        $restaurants = $this->restaurantService->index($request->only([
            'country_id', 'restaurant_type_id', 'delivery_available', 'search', 'per_page'
        ]));

        return $this->success($restaurants);
    }

    /**
     * Search restaurants by name
     */
    public function search(Request $request): JsonResponse
    {
        $restaurants = $this->restaurantService->search($request->only(['name', 'per_page']));

        return $this->success($restaurants);
    }

    /**
     * Get restaurants within radius (km) of latitude/longitude
     */
    public function getNearby(GetNearbyRestaurantRequest $request): JsonResponse
    {
        $restaurants = $this->restaurantService->getNearby(
            $request->latitude,
            $request->longitude,
            $request->radius
        );

        return $this->success($restaurants);
    }

    /**
     * Get Restaurant Details (with best_sellers, outside_images, inside_images)
     */
    public function show(int $id): JsonResponse
    {
        $data = $this->restaurantService->show($id);

        return $this->success($data);
    }

    /**
     * Create restaurant (owner). Status pending until admin approval.
     */
    public function store(StoreRestaurantRequest $request): JsonResponse
    {
        $restaurant = $this->restaurantService->store($request->user(), $request->validated());

        return $this->created(['restaurant' => $restaurant], 'Restaurant created successfully. Awaiting admin approval.');
    }

    /**
     * Update restaurant (owner or admin).
     */
    public function update(UpdateRestaurantRequest $request, int $id): JsonResponse
    {
        $restaurant = $this->restaurantService->update(
            $request->user(),
            $id,
            $request->validated()
        );

        return $this->success(['restaurant' => $restaurant], 'Restaurant updated successfully');
    }

    /**
     * Delete restaurant (owner or admin).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->restaurantService->destroy($request->user(), $id);

        return $this->success(null, 'Restaurant deleted successfully');
    }
}
