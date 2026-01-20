<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'code' => 'VN',
                'name' => 'Vietnam',
                'cctld' => '.vn',
                'phone_code' => '+84',
                'currency_code' => 'VND',
                'currency_symbol' => '₫',
                'is_active' => true,
            ],
            [
                'code' => 'KR',
                'name' => 'South Korea',
                'cctld' => '.kr',
                'phone_code' => '+82',
                'currency_code' => 'KRW',
                'currency_symbol' => '₩',
                'is_active' => true,
            ],
            [
                'code' => 'US',
                'name' => 'United States',
                'cctld' => '.us',
                'phone_code' => '+1',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'is_active' => true,
            ],
            [
                'code' => 'JP',
                'name' => 'Japan',
                'cctld' => '.jp',
                'phone_code' => '+81',
                'currency_code' => 'JPY',
                'currency_symbol' => '¥',
                'is_active' => true,
            ],
            [
                'code' => 'CN',
                'name' => 'China',
                'cctld' => '.cn',
                'phone_code' => '+86',
                'currency_code' => 'CNY',
                'currency_symbol' => '¥',
                'is_active' => true,
            ],
            [
                'code' => 'TH',
                'name' => 'Thailand',
                'cctld' => '.th',
                'phone_code' => '+66',
                'currency_code' => 'THB',
                'currency_symbol' => '฿',
                'is_active' => true,
            ],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                $country
            );
        }
    }
}
