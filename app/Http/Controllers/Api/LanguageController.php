<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;

/**
 * @group Endpoints
 */
class LanguageController extends Controller
{
    /**
     * GET api/languages
     * 
     * Get list of all active languages
     */
    public function index()
    {
        $languages = Language::active()
            ->orderBy('sort_order')
            ->get();

        return response()->json($languages);
    }

    /**
     * GET api/languages/{code}
     * 
     * Get language details by code
     * 
     * @urlParam code integer required The language code. Example: 17
     */
    public function show($code)
    {
        $language = Language::where('code', $code)->firstOrFail();

        return response()->json($language);
    }
}
