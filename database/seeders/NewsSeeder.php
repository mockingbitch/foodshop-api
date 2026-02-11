<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'type' => 'news',
                'category_id' => null,
                'title' => ['en' => 'Welcome to FoodShop', 'vn' => 'Chào mừng đến FoodShop', 'kr' => 'FoodShop에 오신 것을 환영합니다'],
                'content' => ['en' => 'We are excited to launch our new platform for restaurants and food lovers.', 'vn' => 'Chúng tôi vui mừng ra mắt nền tảng mới cho nhà hàng và người yêu ẩm thực.', 'kr' => '우리는 레스토랑과 음식 애호가를 위한 새 플랫폼을 출시하게 되어 기쁩니다.'],
                'excerpt' => ['en' => 'New platform launch', 'vn' => 'Ra mắt nền tảng mới', 'kr' => '새 플랫폼 출시'],
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ],
            [
                'type' => 'news',
                'category_id' => null,
                'title' => ['en' => 'Best Restaurants 2025', 'vn' => 'Nhà hàng hay nhất 2025', 'kr' => '2025 최고의 레스토랑'],
                'content' => ['en' => 'Discover the top-rated restaurants in your city.', 'vn' => 'Khám phá các nhà hàng được đánh giá cao nhất trong thành phố của bạn.', 'kr' => '당신의 도시에서 최고 평점의 레스토랑을 발견하세요.'],
                'excerpt' => ['en' => 'Top restaurants guide', 'vn' => 'Hướng dẫn nhà hàng hàng đầu', 'kr' => '최고 레스토랑 가이드'],
                'status' => 'published',
                'published_at' => now()->subDays(2),
            ],
            [
                'type' => 'news',
                'category_id' => null,
                'title' => ['en' => 'Upcoming Food Festival', 'vn' => 'Lễ hội ẩm thực sắp tới', 'kr' => '다가오는 푸드 페스티벌'],
                'content' => ['en' => 'Save the date for our annual food festival next month.', 'vn' => 'Đánh dấu lịch cho lễ hội ẩm thực thường niên vào tháng tới.', 'kr' => '다음 달 연례 푸드 페스티벌 날짜를 저장하세요.'],
                'excerpt' => ['en' => 'Food festival announcement', 'vn' => 'Thông báo lễ hội ẩm thực', 'kr' => '푸드 페스티벌 공지'],
                'status' => 'draft',
                'published_at' => null,
            ],
            [
                'type' => 'course',
                'category_id' => null,
                'title' => ['en' => 'Vietnamese Cooking Basics', 'vn' => 'Cơ bản nấu ăn Việt Nam', 'kr' => '베트남 요리 기초'],
                'content' => ['en' => 'Learn the fundamentals of Vietnamese cuisine in this 4-hour workshop.', 'vn' => 'Học những điều cơ bản về ẩm thực Việt Nam trong workshop 4 giờ này.', 'kr' => '이 4시간 워크숍에서 베트남 요리의 기초를 배우세요.'],
                'excerpt' => ['en' => '4-hour cooking workshop', 'vn' => 'Workshop nấu ăn 4 giờ', 'kr' => '4시간 요리 워크숍'],
                'course_price' => 50.00,
                'course_duration' => 4,
                'max_participants' => 12,
                'status' => 'published',
                'published_at' => now()->subDay(1),
            ],
            [
                'type' => 'chef',
                'category_id' => null,
                'title' => ['en' => 'Chef Minh Nguyen', 'vn' => 'Đầu bếp Minh Nguyễn', 'kr' => '셰프 민 응우옌'],
                'content' => ['en' => 'Award-winning chef with 15 years of experience in Asian fusion.', 'vn' => 'Đầu bếp đoạt giải với 15 năm kinh nghiệm về ẩm thực Á fusion.', 'kr' => '아시아 퓨전 15년 경력의 수상 경력 셰프.'],
                'excerpt' => ['en' => 'Meet our head chef', 'vn' => 'Gặp đầu bếp trưởng', 'kr' => '헤드 셰프를 만나보세요'],
                'chef_name' => 'Minh Nguyen',
                'chef_specialty' => 'Asian Fusion',
                'status' => 'published',
                'published_at' => now()->subDays(3),
            ],
        ];

        foreach ($items as $item) {
            News::create(array_merge($item, ['view_count' => 0]));
        }
    }
}
