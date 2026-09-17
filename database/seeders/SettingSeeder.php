<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate([], [
            'store_name' => 'Mini Mart',
            'hours' => '7:00 AM – 10:00 PM',
            'delivery_rate' => (float) env('MINIMART_DELIVERY_RATE', 10),
            'low_stock_threshold' => (int) env('MINIMART_LOW_STOCK_THRESHOLD', 10),
        ]);
    }
}
