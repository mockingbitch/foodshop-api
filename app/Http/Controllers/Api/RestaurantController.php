<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Restaurant\IndexRestaurantRequest;
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
     * Get List of Restaurants (paginated unless per_page=all).
     * Filters: owner_id, country_id, restaurant_type_id, delivery_available, search, lat, lng, radius (nearby), per_page
     */
    public function index(IndexRestaurantRequest $request): JsonResponse
    {
        $restaurants = $this->restaurantService->index($request->validated());

        return $this->successList($restaurants);
    }

    /**
     * Search restaurants (same filters as index). Paginated unless per_page=all.
     */
    public function search(IndexRestaurantRequest $request): JsonResponse
    {
        $restaurants = $this->restaurantService->index($request->validated());

        return $this->successList($restaurants);
    }

    /**
     * Get restaurants within radius (km) of lat/lng (paginated).
     */
    public function getNearby(GetNearbyRestaurantRequest $request): JsonResponse
    {
        $restaurants = $this->restaurantService->getNearby(
            $request->lat,
            $request->lng,
            $request->input('radius', 10),
            (int) $request->input('per_page', 15)
        );

        return $this->successList($restaurants);
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
     * Create restaurant (owner). Status active mặc định.
     */
    public function store(StoreRestaurantRequest $request): JsonResponse
    {
        $restaurant = $this->restaurantService->store($request->user(), $request->validated());

        return $this->created(['restaurant' => $restaurant], 'Restaurant created successfully.');
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

    /**
     * Get list of restaurants belonging to the authenticated owner (dashboard). Always all statuses; newest first.
     */
    public function ownerRestaurants(Request $request): JsonResponse
    {
        $restaurants = $this->restaurantService->getByOwner($request->user(), $request->only(['per_page']));

        return $this->success($restaurants);
    }
}
