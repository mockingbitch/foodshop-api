<?php

namespace App\Repositories;

use App\Contracts\Repositories\RestaurantRepositoryInterface;
use App\Models\Restaurant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Restaurant repository: Eloquent query layer for Restaurant model.
 * Handles list (filtered), search, nearby, show with relations, create, update, delete, admin list, last code by country.
 */
class RestaurantRepository extends BaseRepository implements RestaurantRepositoryInterface
{
    public function __construct(Restaurant $model)
    {
        parent::__construct($model);
    }

    /**
     * List of active restaurants with filters. Paginated unless per_page=all.
     *
     * @param array $filters country_id?, restaurant_type_id?, delivery_available?, search?, per_page? (int or 'all')
     * @return LengthAwarePaginator|EloquentCollection
     */
    public function getActivePaginated(array $filters): LengthAwarePaginator|EloquentCollection
    {
        $query = $this->query()->with(['country', 'restaurantType', 'user'])->active();

        if (! empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }
        if (! empty($filters['restaurant_type_id'])) {
            $query->where('restaurant_type_id', $filters['restaurant_type_id']);
        }
        if (isset($filters['delivery_available'])) {
            $query->where('delivery_available', (bool) $filters['delivery_available']);
        }
        if (! empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name->en', 'like', $search)
                    ->orWhere('name->vn', 'like', $search)
                    ->orWhere('name->kr', 'like', $search)
                    ->orWhere('city', 'like', $search);
            });
        }

        if (isset($filters['per_page']) && (string) $filters['per_page'] === 'all') {
            return $query->get();
        }
        return $query->paginate((int) ($filters['per_page'] ?? 15));
    }

    /**
     * Search active restaurants by name (JSON fields). Paginated unless per_page=all.
     *
     * @param array $filters name?, per_page? (int or 'all')
     * @return LengthAwarePaginator|EloquentCollection
     */
    public function searchByName(array $filters): LengthAwarePaginator|EloquentCollection
    {
        $query = $this->query()->with(['country', 'restaurantType'])->active();

        if (! empty($filters['name'])) {
            $name = '%' . $filters['name'] . '%';
            $query->where(function ($q) use ($name) {
                $q->where('name->en', 'like', $name)
                    ->orWhere('name->vn', 'like', $name)
                    ->orWhere('name->kr', 'like', $name);
            });
        }

        if (isset($filters['per_page']) && (string) $filters['per_page'] === 'all') {
            return $query->get();
        }
        return $query->paginate((int) ($filters['per_page'] ?? 15));
    }

    /**
     * Get active restaurants within radius (km) of lat/long.
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $radiusKm
     * @return EloquentCollection
     */
    public function getNearby(float $latitude, float $longitude, float $radiusKm): EloquentCollection
    {
        return $this->query()
            ->with(['country', 'restaurantType'])
            ->active()
            ->nearby($latitude, $longitude, $radiusKm)
            ->get();
    }

    /**
     * Find restaurant by ID with relations (country, restaurantType, user, menus, reviews).
     *
     * @param int $id
     * @return Restaurant
     */
    public function findWithRelations(int $id): Restaurant
    {
        return $this->query()
            ->with(['country', 'restaurantType', 'user', 'menus', 'reviews'])
            ->findOrFail($id);
    }

    /**
     * Get all restaurants (admin) with optional status filter. Paginated unless per_page=all.
     *
     * @param array $filters status?, per_page? (int or 'all')
     * @return LengthAwarePaginator|EloquentCollection
     */
    public function getAllPaginated(array $filters): LengthAwarePaginator|EloquentCollection
    {
        $query = $this->query()->with(['country', 'restaurantType', 'user']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['per_page']) && (string) $filters['per_page'] === 'all') {
            return $query->get();
        }
        return $query->paginate((int) ($filters['per_page'] ?? 15));
    }

    /**
     * Find restaurant with food items relation (for admin).
     *
     * @param int $id
     * @return Restaurant
     */
    public function findWithFoodItems(int $id): Restaurant
    {
        return $this->query()
            ->with(['foodItems.foodCategory'])
            ->findOrFail($id);
    }

    /**
     * Get last restaurant by code prefix (e.g. VN-) for generating next code.
     *
     * @param string $codePrefix e.g. "VN"
     * @return Restaurant|null
     */
    public function getLastByCodePrefix(string $codePrefix): ?Restaurant
    {
        return $this->query()
            ->where('code', 'LIKE', "{$codePrefix}-%")
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Count all restaurants.
     */
    public function count(): int
    {
        return $this->query()->count();
    }

    /**
     * Count active restaurants.
     */
    public function countActive(): int
    {
        return $this->query()->active()->count();
    }

    /**
     * Count pending restaurants.
     */
    public function countPending(): int
    {
        return $this->query()->pending()->count();
    }

    /**
     * Count hidden restaurants.
     */
    public function countHidden(): int
    {
        return $this->query()->hidden()->count();
    }
}
