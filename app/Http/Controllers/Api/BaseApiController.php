<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;

/**
 * Base controller for API endpoints. Provides standardised JSON response helpers.
 */
abstract class BaseApiController extends Controller
{
    use ApiResponseTrait;
}
