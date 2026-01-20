<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RestaurantType;

class RestaurantTypeController extends Controller
{
    public function index()
    {
        $types = RestaurantType::active()->get();

        return response()->json($types);
    }
}
