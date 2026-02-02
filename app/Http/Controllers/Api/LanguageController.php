<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LanguageService;
use Illuminate\Http\JsonResponse;

/**
 * Reference data: list active languages (by sort_order), show language by code.
 *
 * @group Reference
 */
class LanguageController extends Controller
{
    public function __construct(
        protected LanguageService $languageService
    ) {}

    /**
     * Get list of all active languages
     */
    public function index(): JsonResponse
    {
        $languages = $this->languageService->index();

        return response()->json($languages);
    }

    /**
     * Get language details by code
     */
    public function show(string $code): JsonResponse
    {
        $language = $this->languageService->show($code);

        return response()->json($language);
    }
}
