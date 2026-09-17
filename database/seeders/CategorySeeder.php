<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fruits',      'icon' => 'fa-apple-whole',     'image' => 'Photo/Fruit/fruite.jpg'],
            ['name' => 'Meats',       'icon' => 'fa-drumstick-bite',  'image' => 'Photo/Meate/meats.jpg'],
            ['name' => 'Frozen',      'icon' => 'fa-snowflake',       'image' => 'Photo/Frozen/Gomgom Mozzeralla cheese sticks.jpg'],
            ['name' => 'Grocery',     'icon' => 'fa-basket-shopping', 'image' => 'Photo/Grocery/grocery.jpg'],
            ['name' => 'Vegetables',  'icon' => 'fa-carrot',          'image' => 'Photo/Fruit/vegetable.jpg'],
            ['name' => 'Snacks',      'icon' => 'fa-cookie-bite',     'image' => 'Photo/Snack/download.jpg'],
            ['name' => 'Drinks',      'icon' => 'fa-bottle-water',    'image' => 'Photo/Drinks/drink.jpg'],
            ['name' => 'Ice Cream',   'icon' => 'fa-ice-cream',       'image' => 'Photo/Icecream/icecream.jpg'],
            ['name' => 'Bakery',      'icon' => 'fa-bread-slice',     'image' => 'Photo/Bekery/backery.jpg'],
            ['name' => 'D&D',         'icon' => 'fa-egg',             'image' => 'Photo/D&D/Nestle Natural Set Yogurt.jpg'],
            ['name' => 'Body Care',   'icon' => 'fa-pump-soap',       'image' => 'Photo/Bodycare/bodycare.jpg'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
