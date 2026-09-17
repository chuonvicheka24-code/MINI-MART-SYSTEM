<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Bananas',                  'cat' => 'Fruits',    'price' => 1.20, 'unit' => 'bunch',      'emoji' => '🍌', 'qty' => 42, 'img' => 'Photo/Fruit/banana.jpg', 'date' => '2026-01-02'],
            ['name' => 'Apples',                   'cat' => 'Fruits',    'price' => 2.80, 'unit' => 'kg',         'emoji' => '🍎', 'qty' => 8,  'img' => 'Photo/Fruit/apple.jpg', 'date' => '2026-01-02'],
            ['name' => 'Oranges',                  'cat' => 'Fruits',    'price' => 2.10, 'unit' => 'kg',         'emoji' => '🍊', 'qty' => 30, 'img' => 'Photo/Fruit/orange.jpg', 'date' => '2026-01-02'],
            ['name' => 'Watermelon',                'cat' => 'Fruits',    'price' => 3.50, 'unit' => 'each',       'emoji' => '🍉', 'qty' => 14, 'img' => 'Photo/Fruit/watermelon.jpg', 'date' => '2026-01-02'],
            ['name' => 'Chicken Breast',            'cat' => 'Meats',     'price' => 5.60, 'unit' => 'kg',         'emoji' => '🍗', 'qty' => 6,  'img' => 'Photo/Meate/chicken.jpg', 'date' => '2026-01-03'],
            ['name' => 'Beef',                      'cat' => 'Meats',     'price' => 9.90, 'unit' => 'kg',         'emoji' => '🥩', 'qty' => 11, 'img' => 'Photo/Meate/rawbeef.jpg', 'date' => '2026-01-03'],
            ['name' => 'Farm Pork Ribs',            'cat' => 'Meats',     'price' => 6.40, 'unit' => 'kg',         'emoji' => '🍖', 'qty' => 19, 'img' => 'Photo/Meate/sachjruk.jpg', 'date' => '2026-01-03'],
            ['name' => 'Sliced Salame',              'cat' => 'Frozen',    'price' => 4.90, 'unit' => '1kg bag',    'emoji' => '🍗', 'qty' => 16, 'img' => 'Photo/Frozen/sliced Salame Napoli Italian.jpg', 'date' => '2026-01-04'],
            ['name' => 'Chicken Wing',               'cat' => 'Frozen',    'price' => 6.80, 'unit' => 'pack of 6',  'emoji' => '🍔', 'qty' => 4,  'img' => 'Photo/Frozen/Tyson Frozen Fully Cooked Buffalo Style Wings.jpg', 'date' => '2026-01-04'],
            ['name' => 'Frozen Mixed Vegetables',   'cat' => 'Frozen',    'price' => 2.60, 'unit' => '1kg bag',    'emoji' => '🥦', 'qty' => 20, 'img' => 'Photo/Frozen/Kirkland Signature Organic Mixed Vegetables.jpg', 'date' => '2026-01-04'],
            ['name' => 'Jasmine Rice',               'cat' => 'Grocery',   'price' => 1.80, 'unit' => '5kg bag',    'emoji' => '🌾', 'qty' => 50, 'img' => 'Photo/Grocery/rice.jpg', 'date' => '2026-01-05'],
            ['name' => 'Olive Oil',                  'cat' => 'Grocery',   'price' => 7.20, 'unit' => '1L',         'emoji' => '🫒', 'qty' => 9,  'img' => 'Photo/Grocery/LabelOliveOil.jpg', 'date' => '2026-01-05'],
            ['name' => 'Tomatoes',                   'cat' => 'Vegetables', 'price' => 1.90, 'unit' => 'kg',        'emoji' => '🍅', 'qty' => 33, 'img' => 'Photo/Vegetable/tomato.jpg', 'date' => '2026-01-06'],
            ['name' => 'White Cauliflower',          'cat' => 'Vegetables', 'price' => 1.50, 'unit' => 'each',      'emoji' => '🥦', 'qty' => 7,  'img' => 'Photo/Vegetable/white_cauliflower.jpg', 'date' => '2026-01-06'],
            ['name' => 'Carrots',                    'cat' => 'Vegetables', 'price' => 1.10, 'unit' => 'kg',        'emoji' => '🥕', 'qty' => 40, 'img' => 'Photo/Vegetable/Carrot.jpg', 'date' => '2026-01-06'],
            ['name' => 'Ginger',                     'cat' => 'Vegetables', 'price' => 1.60, 'unit' => 'kg',        'emoji' => '🍠', 'qty' => 22, 'img' => 'Photo/Vegetable/Ginger.jpg', 'date' => '2026-01-06'],
            ['name' => 'Cereal Oreo Golden',         'cat' => 'Snacks',    'price' => 2.30, 'unit' => 'pack',       'emoji' => '🍘', 'qty' => 26, 'img' => 'Photo/Snack/Cereal Oreo Golden.jpg', 'date' => '2026-01-07'],
            ['name' => "Oreo O's Cereal",            'cat' => 'Snacks',    'price' => 4.80, 'unit' => '250g',       'emoji' => '🥜', 'qty' => 5,  'img' => "Photo/Snack/Oreo O's Cereal.jpg", 'date' => '2026-01-07'],
            ['name' => 'Dark Chocolate Bar',         'cat' => 'Snacks',    'price' => 2.90, 'unit' => 'each',       'emoji' => '🍫', 'qty' => 31, 'img' => 'Photo/Snack/Ferrero Rocher Chocolate 8.jpg', 'date' => '2026-01-07'],
            ['name' => 'Cambodia Water',             'cat' => 'Drinks',    'price' => 0.90, 'unit' => '1.5L',       'emoji' => '💧', 'qty' => 60, 'img' => 'Photo/Drinks/cambodiawater.jpg', 'date' => '2026-01-08'],
            ['name' => 'Oat Milk',                   'cat' => 'Drinks',    'price' => 3.40, 'unit' => '330ml',      'emoji' => '🥤', 'qty' => 13, 'img' => 'Photo/Drinks/OATSIDE barista Blend Oat Miilk.jpg', 'date' => '2026-01-08'],
            ['name' => 'Fanta',                      'cat' => 'Drinks',    'price' => 3.10, 'unit' => '1L',         'emoji' => '🧃', 'qty' => 9,  'img' => 'Photo/Drinks/fanta.jpg', 'date' => '2026-01-08'],
            ['name' => 'Breata Alice Ice',            'cat' => 'Ice Cream', 'price' => 5.20, 'unit' => '500ml tub',  'emoji' => '🍦', 'qty' => 15, 'img' => 'Photo/Icecream/Breata Alice ice.jpg', 'date' => '2026-01-09'],
            ['name' => 'Strawberry Ice Cream',       'cat' => 'Ice Cream', 'price' => 5.50, 'unit' => '500ml tub',  'emoji' => '🍨', 'qty' => 6,  'img' => 'Photo/Icecream/Strawberry Ice cream.jpg', 'date' => '2026-01-09'],
            ['name' => 'Mini Chocolate Croissants',  'cat' => 'Bakery',    'price' => 3.60, 'unit' => 'each',       'emoji' => '🍞', 'qty' => 12, 'img' => 'Photo/Bekery/Marketside Mini Chocolate Croissants.jpg', 'date' => '2026-01-10'],
            ['name' => 'Butter Croissants',          'cat' => 'Bakery',    'price' => 4.20, 'unit' => 'pack of 4',  'emoji' => '🥐', 'qty' => 8,  'img' => 'Photo/Bekery/Marketside All Butter Croissants.jpg', 'date' => '2026-01-10'],
            ['name' => 'Emborg Greek',               'cat' => 'D&D',       'price' => 2.40, 'unit' => 'tray of 12', 'emoji' => '🥚', 'qty' => 24, 'img' => 'Photo/D&D/Emborg Greek.jpg', 'date' => '2026-01-11'],
            ['name' => 'Kraft Singles Cheese',       'cat' => 'D&D',       'price' => 1.70, 'unit' => '1L',         'emoji' => '🥛', 'qty' => 28, 'img' => 'Photo/D&D/Kraft Singles Cheese.jpg', 'date' => '2026-01-11'],
            ['name' => 'Cheddar Cheese',             'cat' => 'D&D',       'price' => 3.90, 'unit' => '200g block', 'emoji' => '🧀', 'qty' => 10, 'img' => 'Photo/D&D/President com queijo cheddar cheese.jpg', 'date' => '2026-01-11'],
            ['name' => 'Nestle Natural Set Yogurt',  'cat' => 'D&D',       'price' => 2.20, 'unit' => '500g tub',   'emoji' => '🥣', 'qty' => 17, 'img' => 'Photo/D&D/Nestle Natural Set Yogurt.jpg', 'date' => '2026-01-11'],
            ['name' => 'Chanel Perform',             'cat' => 'Body Care', 'price' => 2.50, 'unit' => 'each',       'emoji' => '🧼', 'qty' => 20, 'img' => 'Photo/Bodycare/Chanel Perform.jpg', 'date' => '2026-01-12'],
            ['name' => 'Clear Man Shampoo',          'cat' => 'Body Care', 'price' => 4.60, 'unit' => '400ml',      'emoji' => '🧴', 'qty' => 14, 'img' => 'Photo/Bodycare/Clear Shampoo Men Anticaspa Ice Cool Menthol 400Ml.jpg', 'date' => '2026-01-12'],
            ['name' => 'Colgate Dental Cream',       'cat' => 'Body Care', 'price' => 1.90, 'unit' => 'each',       'emoji' => '🪥', 'qty' => 25, 'img' => 'Photo/Bodycare/Colgate Strong Teeth Dental Cream.jpg', 'date' => '2026-01-12'],
        ];

        foreach ($products as $p) {
            $category = Category::where('name', $p['cat'])->first();
            if (! $category) {
                continue;
            }

            Product::updateOrCreate(
                ['name' => $p['name'], 'category_id' => $category->id],
                [
                    'price' => $p['price'],
                    'unit' => $p['unit'],
                    'emoji' => $p['emoji'],
                    'qty' => $p['qty'],
                    'image' => $p['img'],
                    'date_added' => $p['date'],
                ]
            );
        }
    }
}
