<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::active()->get();

        return response()->json($countries);
    }

    public function show($id)
    {
        $country = Country::findOrFail($id);

        return response()->json($country);
    }
}
