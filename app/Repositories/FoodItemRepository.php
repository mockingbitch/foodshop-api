<?php

namespace App\Repositories;

use App\Contracts\Repositories\FoodItemRepositoryInterface;
use App\Models\FoodItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Food item repository: Eloquent query layer for FoodItem model.
 * Handles list (filtered), by category, best seller, find with relations, related products, pending codes, by restaurant.
 */
class FoodItemRepository extends BaseRepository implements FoodItemRepositoryInterface
{
    public function __construct(FoodItem $model)
    {
        parent::__construct($model);
    }

    /**
     * List of active food items with confirmed code and filters. Paginated unless per_page=all.
     *
     * @param array $filters restaurant_id?, category_id?, best_seller?, vegetarian?, search?, per_page? (int or 'all')
     * @return LengthAwarePaginator|EloquentCollection
     */
    public function getActiveConfirmedPaginated(array $filters): LengthAwarePaginator|EloquentCollection
    {
        $query = $this->query()
            ->with(['restaurant', 'foodCategory'])
            ->active()
            ->confirmedCode();

        if (!empty($filters['restaurant_id'])) {
            $query->where('restaurant_id', $filters['restaurant_id']);
        }
        if (!empty($filters['category_id'])) {
            $query->where('food_category_id', $filters['category_id']);
        }
        if (!empty($filters['best_seller'])) {
            $query->bestSeller();
        }
        if (!empty($filters['vegetarian'])) {
            $query->vegetarian();
        }
        if (! empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name->en', 'like', $search)
                    ->orWhere('name->vn', 'like', $search)
                    ->orWhere('name->kr', 'like', $search)
                    ->orWhere('food_code', 'like', $search);
            });
        }

        if (isset($filters['per_page']) && (string) $filters['per_page'] === 'all') {
            return $query->get();
        }
        return $query->paginate((int) ($filters['per_page'] ?? 15));
    }

    /**
     * Get active confirmed food items by category ID (paginated).
     *
     * @param int $categoryId
     * @return LengthAwarePaginator
     */
    public function getByCategory(int $categoryId): LengthAwarePaginator
    {
        return $this->query()
            ->with(['restaurant', 'foodCategory'])
            ->active()
            ->confirmedCode()
            ->where('food_category_id', $categoryId)
            ->paginate(15);
    }

    /**
     * Get best seller food items with optional restaurant filter. Paginated unless per_page=all.
     *
     * @param array $filters restaurant_id?, per_page? (int or 'all')
     * @return LengthAwarePaginator|EloquentCollection
     */
    public function getBestSellerPaginated(array $filters): LengthAwarePaginator|EloquentCollection
    {
        $query = $this->query()
            ->with(['restaurant', 'foodCategory'])
            ->active()
            ->confirmedCode()
            ->bestSeller();

        if (!empty($filters['restaurant_id'])) {
            $query->where('restaurant_id', $filters['restaurant_id']);
        }

        if (isset($filters['per_page']) && (string) $filters['per_page'] === 'all') {
            return $query->get();
        }
        return $query->paginate((int) ($filters['per_page'] ?? 15));
    }

    /**
     * Find food item by ID with relations (restaurant, foodCategory, reviews).
     *
     * @param int $id
     * @return FoodItem
     */
    public function findWithRelations(int $id): FoodItem
    {
        return $this->query()
            ->with(['restaurant', 'foodCategory', 'reviews'])
            ->findOrFail($id);
    }

    /**
     * Get related food items (same category, excluding id, limit 6).
     *
     * @param int $foodCategoryId
     * @param int $excludeId
     * @return Collection
     */
    public function getRelatedByCategory(int $foodCategoryId, int $excludeId): EloquentCollection
    {
        return $this->query()
            ->with(['restaurant'])
            ->active()
            ->confirmedCode()
            ->where('food_category_id', $foodCategoryId)
            ->where('id', '!=', $excludeId)
            ->limit(6)
            ->get();
    }

    /**
     * Get food items with pending code confirmation (admin).
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPendingCodeConfirmation(int $perPage = 20): LengthAwarePaginator
    {
        return $this->query()
            ->with(['restaurant', 'foodCategory'])
            ->pendingCodeConfirmation()
            ->paginate($perPage);
    }

    /**
     * Get food items by restaurant ID with optional status filter (admin).
     *
     * @param int $restaurantId
     * @param array $filters status?
     * @return LengthAwarePaginator
     */
    public function getByRestaurantId(int $restaurantId, array $filters = []): LengthAwarePaginator
    {
        $query = $this->query()
            ->with(['foodCategory'])
            ->where('restaurant_id', $restaurantId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate(20);
    }

    /**
     * Get last food item by code prefix for generating next code (e.g. VN-R001-0001-).
     *
     * @param string $codePrefix
     * @return FoodItem|null
     */
    public function getLastByCodePrefix(string $codePrefix): ?FoodItem
    {
        return $this->query()
            ->where('food_code', 'LIKE', "{$codePrefix}%")
            ->orderBy('id', 'desc')
            ->first();
    }

    /** Count all food items. */
    public function count(): int
    {
        return $this->query()->count();
    }

    /** Count active food items. */
    public function countActive(): int
    {
        return $this->query()->active()->count();
    }

    /** Count pending food items. */
    public function countPending(): int
    {
        return $this->query()->pending()->count();
    }

    /** Count pending code confirmation. */
    public function countPendingCodeConfirmation(): int
    {
        return $this->query()->pendingCodeConfirmation()->count();
    }
}
