<?php

namespace App\Contracts\Repositories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Collection;

/**
 * Menu repository contract.
 */
interface MenuRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active menus by restaurant ID ordered by sort_order.
     *
     * @param int $restaurantId
     * @return Collection
     */
    public function getActiveByRestaurantId(int $restaurantId): Collection;

    /**
     * Find menu by ID with restaurant relation.
     *
     * @param int $id
     * @return Menu
     */
    public function findWithRestaurant(int $id): Menu;
}
