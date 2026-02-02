<?php

namespace App\Repositories;

use App\Contracts\Repositories\MenuRepositoryInterface;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Collection;

/**
 * Menu repository: Eloquent query layer for Menu model.
 * Handles get by restaurant, find with restaurant, create, update, delete.
 */
class MenuRepository extends BaseRepository implements MenuRepositoryInterface
{
    public function __construct(Menu $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active menus by restaurant ID ordered by sort_order.
     *
     * @param int $restaurantId
     * @return Collection
     */
    public function getActiveByRestaurantId(int $restaurantId): Collection
    {
        return $this->query()
            ->where('restaurant_id', $restaurantId)
            ->active()
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Find menu by ID with restaurant relation.
     *
     * @param int $id
     * @return Menu
     */
    public function findWithRestaurant(int $id): Menu
    {
        return $this->query()->with(['restaurant'])->findOrFail($id);
    }
}
