<?php

namespace Database\Seeders;

use App\Models\FoodCategory;
use App\Models\FoodCategoryTranslation;
use Illuminate\Database\Seeder;

class FoodCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'code' => 'appetizer',
                'sort_order' => 1,
                'translations' => [
                    'EN' => ['name' => 'Appetizer', 'description' => 'Starters and light bites'],
                    'VN' => ['name' => 'Khai vị', 'description' => 'Món khai vị và ăn nhẹ'],
                    'KR' => ['name' => '에피타이저', 'description' => '전채 요리와 가벼운 음식'],
                ],
            ],
            [
                'code' => 'maincourse',
                'sort_order' => 2,
                'translations' => [
                    'EN' => ['name' => 'Main Course', 'description' => 'Main dishes and entrees'],
                    'VN' => ['name' => 'Món chính', 'description' => 'Món chính và món ăn chính'],
                    'KR' => ['name' => '메인 요리', 'description' => '메인 요리와 메인 디시'],
                ],
            ],
            [
                'code' => 'soup',
                'sort_order' => 3,
                'translations' => [
                    'EN' => ['name' => 'Soup', 'description' => 'Soups and broths'],
                    'VN' => ['name' => 'Canh / Súp', 'description' => 'Các món canh và súp'],
                    'KR' => ['name' => '수프', 'description' => '수프와 국물 요리'],
                ],
            ],
            [
                'code' => 'salad',
                'sort_order' => 4,
                'translations' => [
                    'EN' => ['name' => 'Salad', 'description' => 'Fresh salads and sides'],
                    'VN' => ['name' => 'Salad', 'description' => 'Salad và món ăn kèm tươi'],
                    'KR' => ['name' => '샐러드', 'description' => '신선한 샐러드와 사이드'],
                ],
            ],
            [
                'code' => 'dessert',
                'sort_order' => 5,
                'translations' => [
                    'EN' => ['name' => 'Dessert', 'description' => 'Sweet desserts and pastries'],
                    'VN' => ['name' => 'Tráng miệng', 'description' => 'Đồ ngọt và bánh'],
                    'KR' => ['name' => '디저트', 'description' => '달콤한 디저트와 페이스트리'],
                ],
            ],
            [
                'code' => 'drink',
                'sort_order' => 6,
                'translations' => [
                    'EN' => ['name' => 'Drinks', 'description' => 'Beverages and drinks'],
                    'VN' => ['name' => 'Đồ uống', 'description' => 'Nước giải khát và đồ uống'],
                    'KR' => ['name' => '음료', 'description' => '음료와 음주'],
                ],
            ],
            [
                'code' => 'side_dish',  // 9 chars
                'sort_order' => 7,
                'translations' => [
                    'EN' => ['name' => 'Side Dish', 'description' => 'Side dishes and accompaniments'],
                    'VN' => ['name' => 'Món phụ', 'description' => 'Món ăn kèm'],
                    'KR' => ['name' => '반찬', 'description' => '반찬과 곁들임 요리'],
                ],
            ],
            [
                'code' => 'noodlerice',  // 10 chars (max)
                'sort_order' => 8,
                'translations' => [
                    'EN' => ['name' => 'Noodles & Rice', 'description' => 'Noodle and rice dishes'],
                    'VN' => ['name' => 'Cơm & Mì', 'description' => 'Các món cơm và mì'],
                    'KR' => ['name' => '면 & 밥', 'description' => '면과 밥 요리'],
                ],
            ],
        ];

        foreach ($categories as $cat) {
            $translations = $cat['translations'];
            unset($cat['translations']);

            $category = FoodCategory::updateOrCreate(
                ['code' => $cat['code']],
                [
                    'sort_order' => $cat['sort_order'],
                    'is_active' => true,
                ]
            );

            foreach ($translations as $lang => $data) {
                FoodCategoryTranslation::updateOrCreate(
                    [
                        'food_category_id' => $category->id,
                        'language_code' => $lang,
                    ],
                    $data
                );
            }
        }
    }
}
