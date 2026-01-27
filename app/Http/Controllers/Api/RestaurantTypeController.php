<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RestaurantType;

/**
 * @group Endpoints
 */
class RestaurantTypeController extends Controller
{
    /**
     * GET api/restaurant-types
     * 
     * Get list of all active restaurant types
     */
    public function index()
    {
        $types = RestaurantType::active()->get();

        return response()->json($types);
    }
}
