<?php

namespace App\Contracts\Repositories;

use App\Models\FoodItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Food item repository contract.
 */
interface FoodItemRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Paginated list of active food items with confirmed code and filters.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getActiveConfirmedPaginated(array $filters): LengthAwarePaginator;

    /**
     * Get active confirmed food items by category ID.
     *
     * @param int $categoryId
     * @return LengthAwarePaginator
     */
    public function getByCategory(int $categoryId): LengthAwarePaginator;

    /**
     * Get best seller food items with optional restaurant filter.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getBestSellerPaginated(array $filters): LengthAwarePaginator;

    /**
     * Find food item by ID with relations.
     *
     * @param int $id
     * @return FoodItem
     */
    public function findWithRelations(int $id): FoodItem;

    /**
     * Get related food items by category, excluding id.
     *
     * @param int $foodCategoryId
     * @param int $excludeId
     * @return Collection
     */
    public function getRelatedByCategory(int $foodCategoryId, int $excludeId): Collection;

    /**
     * Get food items with pending code confirmation.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPendingCodeConfirmation(int $perPage = 20): LengthAwarePaginator;

    /**
     * Get food items by restaurant ID with optional status filter.
     *
     * @param int $restaurantId
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getByRestaurantId(int $restaurantId, array $filters = []): LengthAwarePaginator;

    /**
     * Get last food item by code prefix.
     *
     * @param string $codePrefix
     * @return FoodItem|null
     */
    public function getLastByCodePrefix(string $codePrefix): ?FoodItem;

    public function count(): int;

    public function countActive(): int;

    public function countPending(): int;

    public function countPendingCodeConfirmation(): int;
}
