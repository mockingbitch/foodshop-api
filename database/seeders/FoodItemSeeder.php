<?php

namespace Database\Seeders;

use App\Models\FoodCategory;
use App\Models\FoodItem;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class FoodItemSeeder extends Seeder
{
    /** Sample dish names [en, vi, ko] for variety */
    private array $dishNames = [
        ['en' => 'Grilled Chicken', 'vi' => 'Gà nướng', 'ko' => '구운 치킨'],
        ['en' => 'Beef Pho', 'vi' => 'Phở bò', 'ko' => '쌀국수 (소고기)'],
        ['en' => 'Spring Rolls', 'vi' => 'Gỏi cuốn', 'ko' => '춘권'],
        ['en' => 'Fried Rice', 'vi' => 'Cơm chiên', 'ko' => '볶음밥'],
        ['en' => 'Kimchi Stew', 'vi' => 'Canh kim chi', 'ko' => '김치찌개'],
        ['en' => 'Bun Bo Hue', 'vi' => 'Bún bò Huế', 'ko' => '분보 후에'],
        ['en' => 'Salad Bowl', 'vi' => 'Salad tô', 'ko' => '샐러드 볼'],
        ['en' => 'Ice Cream', 'vi' => 'Kem', 'ko' => '아이스크림'],
        ['en' => 'Fresh Juice', 'vi' => 'Nước ép trái cây', 'ko' => '생과일 주스'],
        ['en' => 'Stir-fried Vegetables', 'vi' => 'Rau xào', 'ko' => '야채 볶음'],
        ['en' => 'Pork Chop', 'vi' => 'Sườn heo', 'ko' => '돈까스'],
        ['en' => 'Seafood Soup', 'vi' => 'Súp hải sản', 'ko' => '해물 수프'],
        ['en' => 'Banana Cake', 'vi' => 'Bánh chuối', 'ko' => '바나나 케이크'],
        ['en' => 'Iced Coffee', 'vi' => 'Cà phê đá', 'ko' => '아이스 커피'],
        ['en' => 'Rice Noodles', 'vi' => 'Bún', 'ko' => '쌀국수'],
        ['en' => 'BBQ Ribs', 'vi' => 'Sườn nướng BBQ', 'ko' => 'BBQ 갈비'],
        ['en' => 'Tom Yum Soup', 'vi' => 'Canh tom yum', 'ko' => '똠얌꿍'],
        ['en' => 'Green Salad', 'vi' => 'Salad xanh', 'ko' => '그린 샐러드'],
        ['en' => 'Mango Sticky Rice', 'vi' => 'Xôi xoài', 'ko' => '망고 찹쌀'],
        ['en' => 'Lemonade', 'vi' => 'Nước chanh', 'ko' => '레모네이드'],
        ['en' => 'Steamed Fish', 'vi' => 'Cá hấp', 'ko' => '찜 fish'],
        ['en' => 'Pad Thai', 'vi' => 'Pad Thái', 'ko' => '팟타이'],
        ['en' => 'Egg Rolls', 'vi' => 'Chả giò', 'ko' => '에그롤'],
        ['en' => 'Curry Rice', 'vi' => 'Cơm cà ri', 'ko' => '카레 rice'],
        ['en' => 'Tofu Soup', 'vi' => 'Canh đậu hũ', 'ko' => '두부 찌개'],
        ['en' => 'Caesar Salad', 'vi' => 'Salad Caesar', 'ko' => '시저 샐러드'],
        ['en' => 'Chocolate Cake', 'vi' => 'Bánh sô cô la', 'ko' => '초콜릿 케이크'],
        ['en' => 'Smoothie', 'vi' => 'Sinh tố', 'ko' => '스무디'],
        ['en' => 'Grilled Salmon', 'vi' => 'Cá hồi nướng', 'ko' => '구운 연어'],
        ['en' => 'Chicken Noodle Soup', 'vi' => 'Phở gà', 'ko' => '치킨 누들 수프'],
        ['en' => 'Vegetable Rice', 'vi' => 'Cơm rau', 'ko' => '야채 밥'],
        ['en' => 'Pancakes', 'vi' => 'Bánh kếp', 'ko' => '팬케이크'],
        ['en' => 'Bubble Tea', 'vi' => 'Trà sữa trân châu', 'ko' => '버블티'],
        ['en' => 'Dumplings', 'vi' => 'Bánh bao', 'ko' => '만두'],
        ['en' => 'Spicy Chicken', 'vi' => 'Gà cay', 'ko' => '매운 치킨'],
        ['en' => 'Miso Soup', 'vi' => 'Canh miso', 'ko' => '미소 수프'],
        ['en' => 'Fruit Salad', 'vi' => 'Salad trái cây', 'ko' => '과일 샐러드'],
        ['en' => 'Tiramisu', 'vi' => 'Tiramisu', 'ko' => '티라미수'],
        ['en' => 'Green Tea', 'vi' => 'Trà xanh', 'ko' => '녹차'],
        ['en' => 'Braised Pork', 'vi' => 'Thịt kho', 'ko' => '브레이즈드 포크'],
        ['en' => 'Bibimbap', 'vi' => 'Bibimbap', 'ko' => '비빔밥'],
        ['en' => 'Crab Soup', 'vi' => 'Súp cua', 'ko' => '게 수프'],
        ['en' => 'Coleslaw', 'vi' => 'Salad bắp cải', 'ko' => '콜슬로'],
        ['en' => 'Cheesecake', 'vi' => 'Bánh phô mai', 'ko' => '치즈케이크'],
        ['en' => 'Iced Tea', 'vi' => 'Trà đá', 'ko' => '아이스티'],
        ['en' => 'Grilled Shrimp', 'vi' => 'Tôm nướng', 'ko' => '구운 새우'],
        ['en' => 'Ramen', 'vi' => 'Ramen', 'ko' => '라멘'],
        ['en' => 'Steamed Rice', 'vi' => 'Cơm trắng', 'ko' => '공기밥'],
        ['en' => 'French Fries', 'vi' => 'Khoai tây chiên', 'ko' => '감자튀김'],
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
                        'vi' => 'Món ăn thơm ngon từ nguyên liệu tươi.',
                        'ko' => '신선한 재료로 만든 맛있는 요리.',
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
