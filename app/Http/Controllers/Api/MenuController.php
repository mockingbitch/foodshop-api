<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\StoreMenuRequest;
use App\Http\Requests\Menu\UpdateMenuRequest;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Restaurant menu CRUD. Owner creates/updates/deletes menus for own restaurant.
 *
 * @group Menus
 */
class MenuController extends Controller
{
    public function __construct(
        protected MenuService $menuService
    ) {}

    /**
     * Get list of menus for a restaurant
     */
    public function getMenus(int $restaurantId): JsonResponse
    {
        $menus = $this->menuService->getMenus($restaurantId);

        return response()->json($menus);
    }

    /**
     * Get menu details by ID
     */
    public function show(int $id): JsonResponse
    {
        $menu = $this->menuService->show($id);

        return response()->json($menu);
    }

    /**
     * Create menu (owner for own restaurant).
     */
    public function store(StoreMenuRequest $request): JsonResponse
    {
        $menu = $this->menuService->store($request->user(), $request->validated());

        return response()->json([
            'message' => 'Menu created successfully',
            'menu' => $menu,
        ], 201);
    }

    /**
     * Update menu (owner or admin).
     */
    public function update(UpdateMenuRequest $request, int $id): JsonResponse
    {
        $menu = $this->menuService->update($request->user(), $id, $request->validated());

        return response()->json([
            'message' => 'Menu updated successfully',
            'menu' => $menu,
        ]);
    }

    /**
     * Delete menu (owner or admin).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->menuService->destroy($request->user(), $id);

        return response()->json(['message' => 'Menu deleted successfully']);
    }
}
