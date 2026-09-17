<?php

namespace Database\Seeders;

use App\Models\DeliveryStaff;
use Illuminate\Database\Seeder;

class DeliveryStaffSeeder extends Seeder
{
    public function run(): void
    {
        $staff = [
            ['name' => 'Pisach Ly', 'phone' => '012 555 101', 'vehicle' => 'Truck TM-1042', 'status' => 'available'],
            ['name' => 'Rithy Van', 'phone' => '012 555 102', 'vehicle' => 'Moto MT-0087', 'status' => 'available'],
        ];

        foreach ($staff as $s) {
            DeliveryStaff::updateOrCreate(['name' => $s['name']], $s);
        }
    }
}
