<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CountryService;
use Illuminate\Http\JsonResponse;

/**
 * Reference data: list active countries, show country by ID.
 *
 * @group Reference
 */
class CountryController extends Controller
{
    public function __construct(
        protected CountryService $countryService
    ) {}

    /**
     * Get list of all active countries
     */
    public function index(): JsonResponse
    {
        $countries = $this->countryService->index();

        return response()->json($countries);
    }

    /**
     * Get country details by ID
     */
    public function show(int $id): JsonResponse
    {
        $country = $this->countryService->show($id);

        return response()->json($country);
    }
}
