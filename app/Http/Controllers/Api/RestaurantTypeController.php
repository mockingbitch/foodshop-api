<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RestaurantTypeService;
use Illuminate\Http\JsonResponse;

/**
 * Reference data: list active restaurant types (General, Snack Bar, Buffet).
 *
 * @group Reference
 */
class RestaurantTypeController extends Controller
{
    public function __construct(
        protected RestaurantTypeService $restaurantTypeService
    ) {}

    /**
     * Get list of all active restaurant types
     */
    public function index(): JsonResponse
    {
        $types = $this->restaurantTypeService->index();

        return response()->json($types);
    }
}
