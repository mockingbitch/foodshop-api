<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Restaurant;
use App\Models\RestaurantType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        $countryIds = Country::pluck('id')->toArray();
        $typeIds = RestaurantType::pluck('id')->toArray();

        // Ensure we have at least one owner (AdminUserSeeder already creates owner@foodshop.com)
        $owners = User::where('role', 'restaurant_owner')->get();
        if ($owners->count() < 20) {
            for ($i = $owners->count() + 1; $i <= 20; $i++) {
                User::firstOrCreate(
                    ['email' => "owner{$i}@foodshop.local"],
                    [
                        'name' => "Restaurant Owner {$i}",
                        'password' => Hash::make('owner123'),
                        'role' => 'restaurant_owner',
                        'phone' => '+84' . str_pad((string) (900000000 + $i), 9, '0'),
                        'is_active' => true,
                    ]
                );
            }
            $owners = User::where('role', 'restaurant_owner')->get();
        }

        $ownerIds = $owners->pluck('id')->toArray();

        $restaurantNames = [
            ['en' => 'Golden Dragon', 'vn' => 'Rồng Vàng', 'kr' => '골든 드래곤'],
            ['en' => 'Pho Paradise', 'vn' => 'Thiên Đường Phở', 'kr' => '퍼 패러다이스'],
            ['en' => 'Seoul Kitchen', 'vn' => 'Bếp Seoul', 'kr' => '서울 키친'],
            ['en' => 'Saigon Bistro', 'vn' => 'Bistro Sài Gòn', 'kr' => '사이공 비스트로'],
            ['en' => 'Tokyo Grill', 'vn' => 'Tokyo Grill', 'kr' => '도쿄 그릴'],
            ['en' => 'Bun Bo House', 'vn' => 'Nhà Bún Bò', 'kr' => '분보 하우스'],
            ['en' => 'Kimchi & Co', 'vn' => 'Kimchi & Co', 'kr' => '김치 앤 코'],
            ['en' => 'Lotus Garden', 'vn' => 'Vườn Sen', 'kr' => '로터스 가든'],
            ['en' => 'Street Food Hub', 'vn' => 'Trung Tâm Ẩm Thực Đường Phố', 'kr' => '스트리트 푸드 허브'],
            ['en' => 'Bamboo Restaurant', 'vn' => 'Nhà Hàng Tre', 'kr' => '밤부 레스토랑'],
            ['en' => 'Spice Route', 'vn' => 'Con Đường Gia Vị', 'kr' => '스파이스 루트'],
            ['en' => 'Ocean View Cafe', 'vn' => 'Cafe View Biển', 'kr' => '오션 뷰 카페'],
            ['en' => 'Morning Star Buffet', 'vn' => 'Buffet Sao Mai', 'kr' => '모닝 스타 뷔페'],
            ['en' => 'Noodle House', 'vn' => 'Nhà Mì', 'kr' => '누들 하우스'],
            ['en' => 'Green Leaf Vegetarian', 'vn' => 'Chay Lá Xanh', 'kr' => '그린 리프 채식'],
            ['en' => 'Hanoi Corner', 'vn' => 'Góc Hà Nội', 'kr' => '하노이 코너'],
            ['en' => 'BBQ Master', 'vn' => 'Bậc Thầy Nướng', 'kr' => 'BBQ 마스터'],
            ['en' => 'Sweet & Savory', 'vn' => 'Ngọt & Mặn', 'kr' => '스위트 앤 세이버리'],
            ['en' => 'Riverside Eats', 'vn' => 'Ẩm Thực Bên Sông', 'kr' => '리버사이드 이츠'],
            ['en' => 'Family Table', 'vn' => 'Bàn Gia Đình', 'kr' => '패밀리 테이블'],
        ];

        $cities = [
            'Ho Chi Minh City', 'Hanoi', 'Da Nang', 'Can Tho', 'Nha Trang',
            'Seoul', 'Busan', 'Incheon', 'Tokyo', 'Osaka',
        ];

        for ($i = 0; $i < 20; $i++) {
            $code = 'R' . str_pad((string) ($i + 1), 3, '0');
            $names = $restaurantNames[$i];
            $city = $cities[$i % count($cities)];

            Restaurant::updateOrCreate(
                ['code' => $code],
                [
                    'user_id' => $ownerIds[$i % count($ownerIds)],
                    'country_id' => $countryIds[$i % count($countryIds)],
                    'restaurant_type_id' => $typeIds[$i % count($typeIds)],
                    'name' => $names,
                    'description' => [
                        'en' => "Cozy restaurant serving quality dishes. A great place to dine with family and friends.",
                        'vn' => "Nhà hàng ấm cúng phục vụ các món ăn chất lượng. Địa điểm lý tưởng cho gia đình và bạn bè.",
                        'kr' => "맛있는 요리를 제공하는 아늑한 레스토랑. 가족과 친구와 함께 식사하기 좋은 곳입니다.",
                    ],
                    'city' => $city,
                    'address' => ($i + 1) . ' Main Street, ' . $city,
                    'phone' => '+84' . str_pad((string) (901000000 + $i), 9, '0'),
                    'email' => "contact@{$code}.local",
                    'delivery_available' => (bool) ($i % 3),
                    'status' => 'active',
                    'rating' => round(3.5 + (mt_rand(0, 150) / 100), 2),
                    'review_count' => mt_rand(10, 500),
                ]
            );
        }
    }
}
