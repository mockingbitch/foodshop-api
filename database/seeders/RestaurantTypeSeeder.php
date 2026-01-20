<?php

namespace Database\Seeders;

use App\Models\RestaurantType;
use Illuminate\Database\Seeder;

class RestaurantTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code' => 'general',
                'name' => [
                    'en' => 'General Restaurant',
                    'vn' => 'Nhà Hàng Chung',
                    'kr' => '일반 레스토랑',
                ],
                'is_active' => true,
            ],
            [
                'code' => 'snack_bar',
                'name' => [
                    'en' => 'Snack Bar',
                    'vn' => 'Quán Ăn Vặt',
                    'kr' => '스낵바',
                ],
                'is_active' => true,
            ],
            [
                'code' => 'buffet',
                'name' => [
                    'en' => 'Buffet',
                    'vn' => 'Buffet',
                    'kr' => '뷔페',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            RestaurantType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
