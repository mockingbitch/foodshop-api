<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;

/**
 * @group Endpoints
 */
class CountryController extends Controller
{
    /**
     * GET api/countries
     * 
     * Get list of all active countries
     */
    public function index()
    {
        $countries = Country::active()->get();

        return response()->json($countries);
    }

    /**
     * GET api/countries/{id}
     * 
     * Get country details by ID
     * 
     * @urlParam id integer required The ID of the country. Example: 17
     */
    public function show($id)
    {
        $country = Country::findOrFail($id);

        return response()->json($country);
    }
}
