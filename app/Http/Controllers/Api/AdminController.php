<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;

/**
 * Admin dashboard statistics (restaurants, food items, news, users, reviews).
 *
 * @group Admin
 */
class AdminController extends Controller
{
    public function __construct(
        protected AdminService $adminService
    ) {}

    /**
     * Get dashboard statistics
     * @authenticated
     */
    public function getStats(): JsonResponse
    {
        $stats = $this->adminService->getStats();

        return response()->json($stats);
    }
}
