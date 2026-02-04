<?php

namespace App\Services;

use App\Contracts\Repositories\MenuRepositoryInterface;
use App\Contracts\Repositories\RestaurantRepositoryInterface;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Restaurant menu business logic: getMenus, show, store, update, destroy. Owner must own restaurant.
 */
class MenuService
{
    public function __construct(
        protected MenuRepositoryInterface $menuRepository,
        protected RestaurantRepositoryInterface $restaurantRepository
    ) {}

    /**
     * Get active menus for restaurant ordered by sort_order.
     *
     * @param int $restaurantId
     * @return Collection
     */
    public function getMenus(int $restaurantId): Collection
    {
        return $this->menuRepository->getActiveByRestaurantId($restaurantId);
    }

    /**
     * Get menu by ID with restaurant.
     *
     * @param int $id
     * @return Menu
     */
    public function show(int $id): Menu
    {
        return $this->menuRepository->findWithRestaurant($id);
    }

    /**
     * Create menu. Throws AuthorizationException if user does not own restaurant and is not admin.
     *
     * @param User $user
     * @param array $data Validated store data
     * @return Menu
     * @throws AuthorizationException
     */
    public function store(User $user, array $data): Menu
    {
        $restaurant = $this->restaurantRepository->findOrFail($data['restaurant_id']);

        if ($restaurant->user_id !== $user->id && !$user->isAdmin()) {
            Log::warning('Menu create unauthorized', ['restaurant_id' => $data['restaurant_id'], 'user_id' => $user->id]);
            throw new AuthorizationException('Unauthorized');
        }

        $menu = $this->menuRepository->create($data);
        Log::info('Menu created', ['menu_id' => $menu->id, 'restaurant_id' => $data['restaurant_id'], 'user_id' => $user->id]);
        return $menu;
    }

    /**
     * Update menu. Throws AuthorizationException if user does not own restaurant and is not admin.
     *
     * @param User $user
     * @param int $id
     * @param array $data
     * @return Menu
     * @throws AuthorizationException
     */
    public function update(User $user, int $id, array $data): Menu
    {
        $menu = $this->menuRepository->findWithRestaurant($id);

        if ($menu->restaurant->user_id !== $user->id && !$user->isAdmin()) {
            Log::warning('Menu update unauthorized', ['menu_id' => $id, 'user_id' => $user->id]);
            throw new AuthorizationException('Unauthorized');
        }

        $menu->update($data);
        Log::info('Menu updated', ['menu_id' => $id, 'user_id' => $user->id]);
        return $menu;
    }

    /**
     * Delete menu. Throws AuthorizationException if user does not own restaurant and is not admin.
     *
     * @param User $user
     * @param int $id
     * @throws AuthorizationException
     */
    public function destroy(User $user, int $id): void
    {
        $menu = $this->menuRepository->findWithRestaurant($id);

        if ($menu->restaurant->user_id !== $user->id && !$user->isAdmin()) {
            Log::warning('Menu delete unauthorized', ['menu_id' => $id, 'user_id' => $user->id]);
            throw new AuthorizationException('Unauthorized');
        }

        $menu->delete();
        Log::info('Menu deleted', ['menu_id' => $id, 'user_id' => $user->id]);
    }
}
