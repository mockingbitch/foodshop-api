<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\FoodItem;
use App\Models\News;
use App\Models\User;
use App\Models\Review;

class AdminController extends Controller
{
    public function getStats()
    {
        $stats = [
            'restaurants' => [
                'total' => Restaurant::count(),
                'active' => Restaurant::active()->count(),
                'pending' => Restaurant::pending()->count(),
                'hidden' => Restaurant::hidden()->count(),
            ],
            'food_items' => [
                'total' => FoodItem::count(),
                'active' => FoodItem::active()->count(),
                'pending' => FoodItem::pending()->count(),
                'pending_code_confirmation' => FoodItem::pendingCodeConfirmation()->count(),
            ],
            'news' => [
                'total' => News::count(),
                'published' => News::published()->count(),
                'draft' => News::draft()->count(),
            ],
            'users' => [
                'total' => User::count(),
                'owners' => User::owners()->count(),
                'admins' => User::admins()->count(),
            ],
            'reviews' => [
                'total' => Review::count(),
                'pending' => Review::pending()->count(),
                'approved' => Review::approved()->count(),
            ],
        ];

        return response()->json($stats);
    }
}
