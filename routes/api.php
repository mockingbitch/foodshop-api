<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\AdminRestaurantController;
use App\Http\Controllers\Api\FoodItemController;
use App\Http\Controllers\Api\AdminFoodItemController;
use App\Http\Controllers\Api\FoodCategoryController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\RestaurantTypeController;
use App\Http\Controllers\Api\ExchangeRateController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AdminReviewController;
use App\Http\Controllers\Api\FileUploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/test', function () {
    return response()->json([
        'message' => 'FoodShop API is running',
        'version' => '1.0.0',
        'timestamp' => now(),
    ]);
});

// ============================================================
// AUTHENTICATION ROUTES
// ============================================================
Route::prefix('auth')->group(function () {
    // Owner Registration & Login
    Route::post('/owner/register', [AuthController::class, 'registerOwner']);
    Route::post('/owner/login', [AuthController::class, 'loginOwner']);
    
    // Admin Login
    Route::post('/admin/login', [AuthController::class, 'loginAdmin']);
    
    // Protected auth routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// Owner Profile (protected)
Route::middleware('auth:api')->prefix('owner')->group(function () {
    Route::put('/profile', [AuthController::class, 'updateOwnerProfile']);
});

// ============================================================
// LANGUAGE & COUNTRY ROUTES (Public)
// ============================================================
Route::get('/languages', [LanguageController::class, 'index']);
Route::get('/languages/{code}', [LanguageController::class, 'show']);

Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{id}', [CountryController::class, 'show']);

Route::get('/restaurant-types', [RestaurantTypeController::class, 'index']);

// ============================================================
// RESTAURANT ROUTES
// ============================================================
// Public restaurant routes
Route::prefix('restaurants')->group(function () {
    Route::get('/', [RestaurantController::class, 'index']);
    Route::get('/search', [RestaurantController::class, 'search']);
    Route::get('/nearby', [RestaurantController::class, 'getNearby']);
    Route::get('/{id}', [RestaurantController::class, 'show']);
    
    // Restaurant reviews
    Route::get('/{restaurantId}/reviews', [ReviewController::class, 'getRestaurantReviews']);
    Route::post('/{restaurantId}/reviews', [ReviewController::class, 'storeRestaurantReview']);
    
    // Restaurant menus
    Route::get('/{restaurantId}/menus', [MenuController::class, 'getMenus']);
});

// Protected restaurant routes (Owner)
Route::middleware('auth:api')->prefix('restaurants')->group(function () {
    Route::post('/', [RestaurantController::class, 'store']);
    Route::put('/{id}', [RestaurantController::class, 'update']);
    Route::delete('/{id}', [RestaurantController::class, 'destroy']);
});

// ============================================================
// FOOD ITEM ROUTES
// ============================================================
// Public food item routes
Route::prefix('food-items')->group(function () {
    Route::get('/', [FoodItemController::class, 'index']);
    Route::get('/search', [FoodItemController::class, 'search']);
    Route::get('/by-category/{categoryId}', [FoodItemController::class, 'getByCategory']);
    Route::get('/best-seller', [FoodItemController::class, 'getBestSeller']);
    Route::get('/{id}', [FoodItemController::class, 'show']);
    
    // Food item reviews
    Route::get('/{foodItemId}/reviews', [ReviewController::class, 'getReviews']);
    Route::post('/{foodItemId}/reviews', [ReviewController::class, 'storeReview']);
});

// Protected food item routes (Owner)
Route::middleware('auth:api')->prefix('food-items')->group(function () {
    Route::post('/', [FoodItemController::class, 'store']);
    Route::put('/{id}', [FoodItemController::class, 'update']);
    Route::delete('/{id}', [FoodItemController::class, 'destroy']);
    Route::post('/{id}/confirm-code', [FoodItemController::class, 'confirmFoodCode']);
});

// ============================================================
// FOOD CATEGORY ROUTES
// ============================================================
// Public category routes (read-only)
Route::prefix('food-categories')->group(function () {
    Route::get('/', [FoodCategoryController::class, 'index']);
    Route::get('/{id}', [FoodCategoryController::class, 'show']);
});

// ============================================================
// NEWS/COURSE/CHEF ROUTES
// ============================================================
// Public news routes (read-only)
Route::prefix('news')->group(function () {
    Route::get('/', [NewsController::class, 'index']);
    Route::get('/by-type/{type}', [NewsController::class, 'getByType']);
    Route::get('/{id}', [NewsController::class, 'show']);
});

// ============================================================
// MENU ROUTES
// ============================================================
Route::middleware('auth:api')->prefix('menus')->group(function () {
    Route::get('/{id}', [MenuController::class, 'show']);
    Route::post('/', [MenuController::class, 'store']);
    Route::put('/{id}', [MenuController::class, 'update']);
    Route::delete('/{id}', [MenuController::class, 'destroy']);
});

// ============================================================
// EXCHANGE RATE ROUTES
// ============================================================
Route::prefix('exchange-rates')->group(function () {
    Route::get('/', [ExchangeRateController::class, 'getExchangeRates']);
    Route::post('/convert', [ExchangeRateController::class, 'convertCurrency']);
});

// ============================================================
// FILE UPLOAD ROUTES (Protected)
// ============================================================
Route::middleware('auth:api')->prefix('upload')->group(function () {
    Route::post('/images', [FileUploadController::class, 'uploadImages']);
    Route::post('/restaurant-images', [FileUploadController::class, 'uploadRestaurantImages']);
    Route::post('/food-images', [FileUploadController::class, 'uploadFoodImages']);
});

// ============================================================
// ADMIN ROUTES (Protected - Admin only)
// ============================================================
Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard/stats', [AdminController::class, 'getStats']);

    // Food Category Management
    Route::get('/food-categories', [FoodCategoryController::class, 'index']);
    Route::get('/food-categories/{id}', [FoodCategoryController::class, 'show']);
    Route::post('/food-categories', [FoodCategoryController::class, 'store']);
    Route::put('/food-categories/{id}', [FoodCategoryController::class, 'update']);
    Route::delete('/food-categories/{id}', [FoodCategoryController::class, 'destroy']);
    Route::post('/food-categories/{id}/translations', [FoodCategoryController::class, 'addTranslation']);

    // News Management
    Route::get('/news', [NewsController::class, 'adminIndex']);
    Route::get('/news/by-type/{type}', [NewsController::class, 'getByType']);
    Route::get('/news/{id}', [NewsController::class, 'show']);
    Route::post('/news', [NewsController::class, 'store']);
    Route::put('/news/{id}', [NewsController::class, 'update']);
    Route::delete('/news/{id}', [NewsController::class, 'destroy']);

    // Restaurant Management
    Route::get('/restaurants', [AdminRestaurantController::class, 'index']);
    Route::get('/restaurants/{restaurantId}/food-items', [AdminRestaurantController::class, 'getRestaurantFoodItems']);
    Route::put('/restaurants/{id}/status', [AdminRestaurantController::class, 'updateStatus']);

    // Food Item Management
    Route::get('/food-items/pending-codes', [FoodItemController::class, 'getPendingFoodCodes']);
    Route::get('/restaurants/{restaurantId}/food-items', [AdminFoodItemController::class, 'getRestaurantFoodItems']);
    Route::put('/food-items/{id}/status', [AdminFoodItemController::class, 'updateStatus']);

    // Review Management
    Route::get('/reviews', [AdminReviewController::class, 'index']);
    Route::get('/reviews/{id}', [AdminReviewController::class, 'show']);
    Route::put('/reviews/{id}/status', [AdminReviewController::class, 'updateStatus']);
    Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy']);
});

// ============================================================
// SEARCH ROUTES
// ============================================================
Route::prefix('search')->group(function () {
    Route::get('/restaurants', [RestaurantController::class, 'search']);
    Route::get('/food-items', [FoodItemController::class, 'search']);
    Route::get('/food-items/by-category', [FoodItemController::class, 'getByCategory']);
    Route::get('/restaurants/by-distance', [RestaurantController::class, 'getNearby']);
});
