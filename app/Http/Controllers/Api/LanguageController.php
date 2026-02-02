<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\LanguageService;
use Illuminate\Http\JsonResponse;

/**
 * Reference data: list active languages (by sort_order), show language by code.
 *
 * @group Reference
 */
class LanguageController extends BaseApiController
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

        return $this->success($languages);
    }

    /**
     * Get language details by code
     */
    public function show(string $code): JsonResponse
    {
        $language = $this->languageService->show($code);

        return $this->success($language);
    }
}
