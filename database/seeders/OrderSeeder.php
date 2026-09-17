<?php

namespace Database\Seeders;

use App\Models\DeliveryStaff;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $truck = DeliveryStaff::where('vehicle', 'Truck TM-1042')->first();
        $moto = DeliveryStaff::where('vehicle', 'Moto MT-0087')->first();

        $sample = [
            ['id' => 1042, 'customer' => 'Sopheak Ly',   'items' => 5, 'total' => 34.60, 'status' => 'pending', 'transport' => 'truck', 'staff' => $truck, 'placed' => now()->subHours(1)],
            ['id' => 1041, 'customer' => 'Dara Chan',    'items' => 2, 'total' => 11.20, 'status' => 'out',     'transport' => 'moto',  'staff' => $moto,  'placed' => now()->subHours(2)],
            ['id' => 1040, 'customer' => 'Maly Sok',     'items' => 9, 'total' => 58.90, 'status' => 'pending', 'transport' => 'truck', 'staff' => $truck, 'placed' => now()->subHours(3)],
            ['id' => 1039, 'customer' => 'Vireak Heng',  'items' => 3, 'total' => 19.75, 'status' => 'done',    'transport' => 'moto',  'staff' => $moto,  'placed' => now()->subDay()],
            ['id' => 1038, 'customer' => 'Channary Pov', 'items' => 7, 'total' => 41.05, 'status' => 'done',    'transport' => 'truck', 'staff' => $truck, 'placed' => now()->subDay()],
        ];

        foreach ($sample as $row) {
            $user = User::where('first_name', explode(' ', $row['customer'])[0])->first();

            $order = Order::updateOrCreate(
                ['id' => $row['id']],
                [
                    'user_id' => $user?->id,
                    'customer_name' => $row['customer'],
                    'customer_email' => $user?->email,
                    'customer_phone' => $user?->phone,
                    'location' => 'Phnom Penh',
                    'address' => 'Veng Sreng Blvd, Phnom Penh',
                    'truck_number' => $row['transport'] === 'truck' ? 'TM-1042' : null,
                    'transport_type' => $row['transport'],
                    'payment_method' => 'Cash on delivery',
                    'subtotal' => round($row['total'] / 1.1, 2),
                    'delivery_fee' => round($row['total'] - ($row['total'] / 1.1), 2),
                    'total' => $row['total'],
                    'status' => $row['status'],
                    'delivery_staff_id' => $row['staff']?->id,
                    'placed_at' => $row['placed'],
                ]
            );

            if ($order->items()->count() === 0) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => null,
                    'product_name' => 'Assorted items',
                    'unit' => 'order',
                    'price' => round($row['total'] / $row['items'], 2),
                    'qty' => $row['items'],
                    'line_total' => $row['total'],
                ]);
            }
        }
    }
}
