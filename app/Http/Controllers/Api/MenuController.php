<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;

/**
 * @group Endpoints
 */
class MenuController extends Controller
{
    /**
     * GET api/restaurants/{restaurantId}/menus
     * 
     * Get list of menus for a restaurant
     * 
     * @urlParam restaurantId integer required The ID of the restaurant. Example: 17
     */
    public function getMenus($restaurantId)
    {
        $menus = Menu::where('restaurant_id', $restaurantId)
            ->active()
            ->orderBy('sort_order')
            ->get();

        return response()->json($menus);
    }

    /**
     * GET api/menus/{id}
     * 
     * Get menu details by ID
     * 
     * @urlParam id integer required The ID of the menu. Example: 17
     */
    public function show($id)
    {
        $menu = Menu::with(['restaurant'])->findOrFail($id);

        return response()->json($menu);
    }

    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|array',
            'name.en' => 'required|string',
            'description' => 'nullable|array',
            'image' => 'nullable|string',
            'sort_order' => 'integer',
        ]);

        // Check if user owns the restaurant
        $restaurant = Restaurant::findOrFail($request->restaurant_id);
        if ($restaurant->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $menu = Menu::create($request->all());

        return response()->json([
            'message' => 'Menu created successfully',
            'menu' => $menu,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        // Check if user owns the restaurant
        if ($menu->restaurant->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|array',
            'description' => 'nullable|array',
            'image' => 'nullable|string',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $menu->update($request->all());

        return response()->json([
            'message' => 'Menu updated successfully',
            'menu' => $menu,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        // Check if user owns the restaurant
        if ($menu->restaurant->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $menu->delete();

        return response()->json([
            'message' => 'Menu deleted successfully',
        ]);
    }
}
