<?php

namespace App\Contracts\Repositories;

use App\Models\Restaurant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Restaurant repository contract.
 */
interface RestaurantRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * List of active restaurants with filters. Paginated unless per_page=all.
     *
     * @param array $filters
     * @return LengthAwarePaginator|Collection
     */
    public function getActivePaginated(array $filters): LengthAwarePaginator|Collection;

    /**
     * Search active restaurants by name. Paginated unless per_page=all.
     *
     * @param array $filters
     * @return LengthAwarePaginator|Collection
     */
    public function searchByName(array $filters): LengthAwarePaginator|Collection;

    /**
     * Get active restaurants within radius of lat/long.
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $radiusKm
     * @return Collection
     */
    public function getNearby(float $latitude, float $longitude, float $radiusKm): Collection;

    /**
     * Find restaurant by ID with relations.
     *
     * @param int $id
     * @return Restaurant
     */
    public function findWithRelations(int $id): Restaurant;

    /**
     * Get all restaurants (admin) with optional status filter. Paginated unless per_page=all.
     *
     * @param array $filters
     * @return LengthAwarePaginator|Collection
     */
    public function getAllPaginated(array $filters): LengthAwarePaginator|Collection;

    /**
     * Find restaurant with food items relation.
     *
     * @param int $id
     * @return Restaurant
     */
    public function findWithFoodItems(int $id): Restaurant;

    /**
     * Get last restaurant by code prefix.
     *
     * @param string $codePrefix
     * @return Restaurant|null
     */
    public function getLastByCodePrefix(string $codePrefix): ?Restaurant;

    public function count(): int;

    public function countActive(): int;

    public function countPending(): int;

    public function countHidden(): int;
}
