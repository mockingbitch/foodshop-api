<?php

namespace App\Services;

use App\Contracts\Repositories\CountryRepositoryInterface;
use App\Contracts\Repositories\RestaurantRepositoryInterface;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Restaurant business logic: list, search, nearby, show, store, update, destroy, admin list/update status.
 */
class RestaurantService
{
    public function __construct(
        protected RestaurantRepositoryInterface $restaurantRepository,
        protected CountryRepositoryInterface $countryRepository
    ) {}

    /**
     * Paginated list of active restaurants with optional filters.
     *
     * @param array $filters country_id?, restaurant_type_id?, delivery_available?, search?, per_page?
     * @return LengthAwarePaginator
     */
    public function index(array $filters): LengthAwarePaginator
    {
        return $this->restaurantRepository->getActivePaginated($filters);
    }

    /**
     * Search restaurants by name (JSON name fields).
     *
     * @param array $filters name?, per_page?
     * @return LengthAwarePaginator
     */
    public function search(array $filters): LengthAwarePaginator
    {
        return $this->restaurantRepository->searchByName($filters);
    }

    /**
     * Get restaurants within radius (km) of latitude/longitude.
     *
     * @param float $latitude
     * @param float $longitude
     * @param float|null $radiusKm Default 10
     * @return Collection
     */
    public function getNearby(float $latitude, float $longitude, ?float $radiusKm = 10): Collection
    {
        return $this->restaurantRepository->getNearby($latitude, $longitude, $radiusKm ?? 10);
    }

    /**
     * Get restaurant detail by ID with best sellers and image arrays.
     *
     * @param int $id
     * @return array{restaurant: Restaurant, best_sellers: Collection, outside_images: array, inside_images: array}
     */
    public function show(int $id): array
    {
        $restaurant = $this->restaurantRepository->findWithRelations($id);

        $bestSellers = $restaurant->foodItems()
            ->active()
            ->bestSeller()
            ->limit(10)
            ->get();

        return [
            'restaurant' => $restaurant,
            'best_sellers' => $bestSellers,
            'outside_images' => $restaurant->getOutsideImages(),
            'inside_images' => $restaurant->getInsideImages(),
        ];
    }

    /**
     * Create restaurant for owner. Status pending until admin approval.
     *
     * @param User $user
     * @param array $data Validated store data
     * @return Restaurant
     */
    public function store(User $user, array $data): Restaurant
    {
        $code = $this->generateRestaurantCode($data['country_id']);

        $restaurant = $this->restaurantRepository->create(array_merge($data, [
            'code' => $code,
            'user_id' => $user->id,
            'status' => 'pending',
        ]));

        return $restaurant->load(['country', 'restaurantType']);
    }

    /**
     * Update restaurant. Throws AuthorizationException if user is not owner or admin.
     *
     * @param User $user
     * @param int $id
     * @param array $data
     * @return Restaurant
     * @throws AuthorizationException
     */
    public function update(User $user, int $id, array $data): Restaurant
    {
        $restaurant = $this->restaurantRepository->findOrFail($id);

        if ($restaurant->user_id !== $user->id && !$user->isAdmin()) {
            throw new AuthorizationException('Unauthorized');
        }

        $restaurant->update($data);

        return $restaurant->load(['country', 'restaurantType']);
    }

    /**
     * Delete restaurant. Throws AuthorizationException if user is not owner or admin.
     *
     * @param User $user
     * @param int $id
     * @throws AuthorizationException
     */
    public function destroy(User $user, int $id): void
    {
        $restaurant = $this->restaurantRepository->findOrFail($id);

        if ($restaurant->user_id !== $user->id && !$user->isAdmin()) {
            throw new AuthorizationException('Unauthorized');
        }

        $restaurant->delete();
    }

    /**
     * Admin: paginated list of all restaurants with optional status filter.
     *
     * @param array $filters status?, per_page?
     * @return LengthAwarePaginator
     */
    public function adminIndex(array $filters): LengthAwarePaginator
    {
        return $this->restaurantRepository->getAllPaginated($filters);
    }

    /**
     * Admin: update restaurant status (active, hidden, pending).
     *
     * @param int $id
     * @param string $status
     * @return Restaurant
     */
    public function updateStatus(int $id, string $status): Restaurant
    {
        $restaurant = $this->restaurantRepository->findOrFail($id);
        $restaurant->update(['status' => $status]);

        return $restaurant;
    }

    /**
     * Admin: get restaurant with paginated food items.
     *
     * @param int $restaurantId
     * @return array{restaurant: Restaurant, food_items: LengthAwarePaginator}
     */
    public function getRestaurantFoodItems(int $restaurantId): array
    {
        $restaurant = $this->restaurantRepository->findWithFoodItems($restaurantId);

        return [
            'restaurant' => $restaurant,
            'food_items' => $restaurant->foodItems()->paginate(20),
        ];
    }

    /**
     * Generate unique restaurant code by country (e.g. VN-0001).
     */
    protected function generateRestaurantCode(int $countryId): string
    {
        $country = $this->countryRepository->findOrFail($countryId);
        $countryCode = $country->code;

        $lastRestaurant = $this->restaurantRepository->getLastByCodePrefix($countryCode);

        $newNumber = $lastRestaurant
            ? (int) substr($lastRestaurant->code, strlen($countryCode) + 1) + 1
            : 1;

        return sprintf('%s-%04d', $countryCode, $newNumber);
    }
}
