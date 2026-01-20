<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::active()
            ->orderBy('sort_order')
            ->get();

        return response()->json($languages);
    }

    public function show($code)
    {
        $language = Language::where('code', $code)->firstOrFail();

        return response()->json($language);
    }
}
