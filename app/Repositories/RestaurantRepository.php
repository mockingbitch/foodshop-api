<?php

namespace App\Repositories;

use App\Contracts\Repositories\RestaurantRepositoryInterface;
use App\Models\Restaurant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * Optional nearby: pass lat + lng (and optionally radius in km) to filter and order by distance.
     *
     * @param array $filters owner_id?, country_id?, restaurant_type_id?, delivery_available?, search?, lat?, lng?, radius?, per_page? (int or 'all')
     * @return LengthAwarePaginator|EloquentCollection
     */
    public function getActivePaginated(array $filters): LengthAwarePaginator|EloquentCollection
    {
        $query = $this->query()->with(['country', 'restaurantType', 'user'])->active();

        if (! empty($filters['owner_id'])) {
            $query->where('user_id', (int) $filters['owner_id']);
        }
        if (! empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }
        if (! empty($filters['restaurant_type_id'])) {
            $query->where('restaurant_type_id', $filters['restaurant_type_id']);
        }
        if (isset($filters['delivery_available'])) {
            $bool = filter_var($filters['delivery_available'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($bool !== null) {
                $query->where('delivery_available', $bool);
            }
        }
        if (! empty($filters['search'])) {
            $pattern = $this->likePatternCaseInsensitive($filters['search']);
            $query->where(function ($q) use ($pattern) {
                $this->addSearchRestaurantNameCity($q, $pattern);
                $this->addSearchByFoodItemName($q, $pattern);
            });
        }

        // Optional nearby: filter by distance and order by distance (only when both lat & lng provided)
        if ($this->hasNearbyFilters($filters)) {
            $query->nearby(
                (float) $filters['lat'],
                (float) $filters['lng'],
                (float) ($filters['radius'] ?? 10)
            );
        }
        $query->orderByDesc('id');

        if (isset($filters['per_page']) && (string) $filters['per_page'] === 'all') {
            return $query->get();
        }
        return $query->paginate((int) ($filters['per_page'] ?? 15));
    }

    /**
     * Check if filters include valid lat and lng for nearby.
     */
    private function hasNearbyFilters(array $filters): bool
    {
        $lat = $filters['lat'] ?? null;
        $lng = $filters['lng'] ?? null;
        return $lat !== null && $lat !== '' && $lng !== null && $lng !== ''
            && is_numeric($lat) && is_numeric($lng);
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
            ->orderByDesc('id')
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

        $query->orderByDesc('id');

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
     * Paginated list of restaurants by owner (user_id). Always all statuses (dashboard); newest first.
     *
     * @param int $userId
     * @param array $filters per_page?
     * @return LengthAwarePaginator
     */
    public function getByOwnerId(int $userId, array $filters = []): LengthAwarePaginator
    {
        return $this->query()
            ->with(['country', 'restaurantType'])
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->paginate($filters['per_page'] ?? 15);
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

    /**
     * Chuẩn hóa từ khóa tìm kiếm: trim, lowercase (UTF-8) để match không phân biệt hoa thường.
     */
    private function likePatternCaseInsensitive(string $keyword): string
    {
        return '%' . mb_strtolower(trim($keyword), 'UTF-8') . '%';
    }

    /**
     * Thêm điều kiện: hoặc nhà hàng có ít nhất một món ăn (active) mà tên món khớp keyword.
     */
    private function addSearchByFoodItemName(\Illuminate\Database\Eloquent\Builder $q, string $pattern): void
    {
        $driver = DB::getDriverName();
        $q->orWhereHas('foodItems', function (\Illuminate\Database\Eloquent\Builder $foodQuery) use ($pattern, $driver) {
            $foodQuery->active();
            if ($driver === 'pgsql') {
                $foodQuery->where(function ($sub) use ($pattern) {
                    $sub->whereRaw('LOWER(COALESCE(name->>\'en\', \'\')) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(COALESCE(name->>\'vn\', \'\')) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(COALESCE(name->>\'kr\', \'\')) LIKE ?', [$pattern]);
                });
            } else {
                $foodQuery->where(function ($sub) use ($pattern) {
                    $sub->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.en"))) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.vn"))) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.kr"))) LIKE ?', [$pattern]);
                });
            }
        });
    }

    /**
     * Thêm điều kiện tìm kiếm theo name (JSON) + city, không phân biệt hoa thường.
     */
    private function addSearchRestaurantNameCity(\Illuminate\Database\Eloquent\Builder $q, string $pattern): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            $q->whereRaw('LOWER(COALESCE(name->>\'en\', \'\')) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(COALESCE(name->>\'vn\', \'\')) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(COALESCE(name->>\'kr\', \'\')) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(COALESCE(city, \'\')) LIKE ?', [$pattern]);
        } else {
            $q->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.en"))) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.vn"))) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.kr"))) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(COALESCE(city, "")) LIKE ?', [$pattern]);
        }
    }

    /**
     * Thêm điều kiện tìm kiếm theo name (JSON), không phân biệt hoa thường.
     */
    private function addSearchRestaurantName(\Illuminate\Database\Eloquent\Builder $q, string $pattern): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            $q->whereRaw('LOWER(COALESCE(name->>\'en\', \'\')) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(COALESCE(name->>\'vn\', \'\')) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(COALESCE(name->>\'kr\', \'\')) LIKE ?', [$pattern]);
        } else {
            $q->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.en"))) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.vn"))) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.kr"))) LIKE ?', [$pattern]);
        }
    }
}
