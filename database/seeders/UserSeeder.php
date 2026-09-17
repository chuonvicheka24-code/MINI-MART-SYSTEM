<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@minimart.test'],
            [
                'first_name' => 'Store',
                'last_name' => 'Admin',
                'phone' => '(123) 456-7890',
                'address' => 'Veng Sreng Blvd, Phnom Penh',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $customers = [
            ['first_name' => 'Sopheak',  'last_name' => 'Ly',   'email' => 'sopheak@example.com',   'phone' => '012 345 001'],
            ['first_name' => 'Dara',     'last_name' => 'Chan', 'email' => 'dara@example.com',      'phone' => '012 345 002'],
            ['first_name' => 'Maly',     'last_name' => 'Sok',  'email' => 'maly@example.com',      'phone' => '012 345 003'],
            ['first_name' => 'Vireak',   'last_name' => 'Heng', 'email' => 'vireak@example.com',    'phone' => '012 345 004'],
            ['first_name' => 'Channary', 'last_name' => 'Pov',  'email' => 'channary@example.com',  'phone' => '012 345 005'],
        ];

        foreach ($customers as $c) {
            User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'first_name' => $c['first_name'],
                    'last_name' => $c['last_name'],
                    'phone' => $c['phone'],
                    'address' => 'Phnom Penh, Cambodia',
                    'password' => Hash::make('password'),
                    'role' => 'customer',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
