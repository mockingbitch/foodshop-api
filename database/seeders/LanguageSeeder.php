<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            [
                'code' => 'EN',
                'name' => 'English',
                'native_name' => 'English',
                'flag_code' => 'US',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'VN',
                'name' => 'Vietnamese',
                'native_name' => 'Tiếng Việt',
                'flag_code' => 'VN',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'KR',
                'name' => 'Korean',
                'native_name' => '한국어',
                'flag_code' => 'KR',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'JP',
                'name' => 'Japanese',
                'native_name' => '日本語',
                'flag_code' => 'JP',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'code' => 'CN',
                'name' => 'Chinese',
                'native_name' => '中文',
                'flag_code' => 'CN',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($languages as $language) {
            Language::updateOrCreate(
                ['code' => $language['code']],
                $language
            );
        }
    }
}
