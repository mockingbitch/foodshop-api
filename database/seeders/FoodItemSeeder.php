<?php

namespace Database\Seeders;

use App\Models\FoodCategory;
use App\Models\FoodItem;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class FoodItemSeeder extends Seeder
{
    /** Sample dish names [en, vn, kr] for variety */
    private array $dishNames = [
        ['en' => 'Grilled Chicken', 'vn' => 'Gà nướng', 'kr' => '구운 치킨'],
        ['en' => 'Beef Pho', 'vn' => 'Phở bò', 'kr' => '쌀국수 (소고기)'],
        ['en' => 'Spring Rolls', 'vn' => 'Gỏi cuốn', 'kr' => '춘권'],
        ['en' => 'Fried Rice', 'vn' => 'Cơm chiên', 'kr' => '볶음밥'],
        ['en' => 'Kimchi Stew', 'vn' => 'Canh kim chi', 'kr' => '김치찌개'],
        ['en' => 'Bun Bo Hue', 'vn' => 'Bún bò Huế', 'kr' => '분보 후에'],
        ['en' => 'Salad Bowl', 'vn' => 'Salad tô', 'kr' => '샐러드 볼'],
        ['en' => 'Ice Cream', 'vn' => 'Kem', 'kr' => '아이스크림'],
        ['en' => 'Fresh Juice', 'vn' => 'Nước ép trái cây', 'kr' => '생과일 주스'],
        ['en' => 'Stir-fried Vegetables', 'vn' => 'Rau xào', 'kr' => '야채 볶음'],
        ['en' => 'Pork Chop', 'vn' => 'Sườn heo', 'kr' => '돈까스'],
        ['en' => 'Seafood Soup', 'vn' => 'Súp hải sản', 'kr' => '해물 수프'],
        ['en' => 'Banana Cake', 'vn' => 'Bánh chuối', 'kr' => '바나나 케이크'],
        ['en' => 'Iced Coffee', 'vn' => 'Cà phê đá', 'kr' => '아이스 커피'],
        ['en' => 'Rice Noodles', 'vn' => 'Bún', 'kr' => '쌀국수'],
        ['en' => 'BBQ Ribs', 'vn' => 'Sườn nướng BBQ', 'kr' => 'BBQ 갈비'],
        ['en' => 'Tom Yum Soup', 'vn' => 'Canh tom yum', 'kr' => '똠얌꿍'],
        ['en' => 'Green Salad', 'vn' => 'Salad xanh', 'kr' => '그린 샐러드'],
        ['en' => 'Mango Sticky Rice', 'vn' => 'Xôi xoài', 'kr' => '망고 찹쌀'],
        ['en' => 'Lemonade', 'vn' => 'Nước chanh', 'kr' => '레모네이드'],
        ['en' => 'Steamed Fish', 'vn' => 'Cá hấp', 'kr' => '찜 fish'],
        ['en' => 'Pad Thai', 'vn' => 'Pad Thái', 'kr' => '팟타이'],
        ['en' => 'Egg Rolls', 'vn' => 'Chả giò', 'kr' => '에그롤'],
        ['en' => 'Curry Rice', 'vn' => 'Cơm cà ri', 'kr' => '카레 rice'],
        ['en' => 'Tofu Soup', 'vn' => 'Canh đậu hũ', 'kr' => '두부 찌개'],
        ['en' => 'Caesar Salad', 'vn' => 'Salad Caesar', 'kr' => '시저 샐러드'],
        ['en' => 'Chocolate Cake', 'vn' => 'Bánh sô cô la', 'kr' => '초콜릿 케이크'],
        ['en' => 'Smoothie', 'vn' => 'Sinh tố', 'kr' => '스무디'],
        ['en' => 'Grilled Salmon', 'vn' => 'Cá hồi nướng', 'kr' => '구운 연어'],
        ['en' => 'Chicken Noodle Soup', 'vn' => 'Phở gà', 'kr' => '치킨 누들 수프'],
        ['en' => 'Vegetable Rice', 'vn' => 'Cơm rau', 'kr' => '야채 밥'],
        ['en' => 'Pancakes', 'vn' => 'Bánh kếp', 'kr' => '팬케이크'],
        ['en' => 'Bubble Tea', 'vn' => 'Trà sữa trân châu', 'kr' => '버블티'],
        ['en' => 'Dumplings', 'vn' => 'Bánh bao', 'kr' => '만두'],
        ['en' => 'Spicy Chicken', 'vn' => 'Gà cay', 'kr' => '매운 치킨'],
        ['en' => 'Miso Soup', 'vn' => 'Canh miso', 'kr' => '미소 수프'],
        ['en' => 'Fruit Salad', 'vn' => 'Salad trái cây', 'kr' => '과일 샐러드'],
        ['en' => 'Tiramisu', 'vn' => 'Tiramisu', 'kr' => '티라미수'],
        ['en' => 'Green Tea', 'vn' => 'Trà xanh', 'kr' => '녹차'],
        ['en' => 'Braised Pork', 'vn' => 'Thịt kho', 'kr' => '브레이즈드 포크'],
        ['en' => 'Bibimbap', 'vn' => 'Bibimbap', 'kr' => '비빔밥'],
        ['en' => 'Crab Soup', 'vn' => 'Súp cua', 'kr' => '게 수프'],
        ['en' => 'Coleslaw', 'vn' => 'Salad bắp cải', 'kr' => '콜슬로'],
        ['en' => 'Cheesecake', 'vn' => 'Bánh phô mai', 'kr' => '치즈케이크'],
        ['en' => 'Iced Tea', 'vn' => 'Trà đá', 'kr' => '아이스티'],
        ['en' => 'Grilled Shrimp', 'vn' => 'Tôm nướng', 'kr' => '구운 새우'],
        ['en' => 'Ramen', 'vn' => 'Ramen', 'kr' => '라멘'],
        ['en' => 'Steamed Rice', 'vn' => 'Cơm trắng', 'kr' => '공기밥'],
        ['en' => 'French Fries', 'vn' => 'Khoai tây chiên', 'kr' => '감자튀김'],
    ];

    public function run(): void
    {
        $restaurantIds = Restaurant::pluck('id')->toArray();
        $categoryIds = FoodCategory::active()->pluck('id')->toArray();

        if (empty($restaurantIds) || empty($categoryIds)) {
            $this->command->warn('Run FoodCategorySeeder and RestaurantSeeder first.');
            return;
        }

        $totalItems = 300;

        for ($i = 1; $i <= $totalItems; $i++) {
            $foodCode = 'FI-' . str_pad((string) $i, 6, '0', STR_PAD_LEFT);
            $template = $this->dishNames[($i - 1) % count($this->dishNames)];
            $price = round(mt_rand(20, 500) / 10, 2); // 2.00 - 50.00

            FoodItem::updateOrCreate(
                ['food_code' => $foodCode],
                [
                    'restaurant_id' => $restaurantIds[($i - 1) % count($restaurantIds)],
                    'food_category_id' => $categoryIds[array_rand($categoryIds)],
                    'food_code_status' => 'confirmed',
                    'name' => $template,
                    'description' => [
                        'en' => 'Delicious dish made with fresh ingredients.',
                        'vn' => 'Món ăn thơm ngon từ nguyên liệu tươi.',
                        'kr' => '신선한 재료로 만든 맛있는 요리.',
                    ],
                    'main_image' => 'food/placeholder.jpg',
                    'extra_images' => null,
                    'price' => $price,
                    'price_usd' => round($price * 0.04, 2), // rough VND->USD
                    'currency_code' => 'USD',
                    'serving_size' => mt_rand(0, 10) ? mt_rand(1, 4) : null,
                    'weight' => mt_rand(0, 5) ? mt_rand(100, 500) : null,
                    'is_vegetarian' => (bool) (($i - 1) % 5 === 0),
                    'is_best_seller' => (bool) (($i - 1) % 7 === 0),
                    'customer_rating' => round(3.2 + (mt_rand(0, 180) / 100), 2),
                    'customer_review_count' => mt_rand(0, 200),
                    'status' => 'active',
                ]
            );
        }
    }
}
