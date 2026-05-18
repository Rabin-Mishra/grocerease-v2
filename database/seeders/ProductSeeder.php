<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // --- Categories ---
        $categoriesData = [
            'Fruits',
            'Vegetables',
            'Tea And Coffee',
            'Dairy Products',
            'Ice-Cream',
            'Meat and Eggs',
            'Sweetner',
        ];

        $categories = [];
        foreach ($categoriesData as $catTitle) {
            $categories[$catTitle] = Category::create([
                'title' => $catTitle,
                'slug' => Str::slug($catTitle)
            ]);
        }

        // --- Brands ---
        $brandsData = [
            'Frutiopia',
            'VegiVista',
            'Tokla Tea',
            'DDC Milk',
            'Bennevis',
            'ABC Butchers',
            'Himalayan Java Coffee',
            'NepSweet',
            'Swastik'
        ];

        $brands = [];
        foreach ($brandsData as $brandTitle) {
            $brands[$brandTitle] = Brand::create([
                'title' => $brandTitle,
                'slug' => Str::slug($brandTitle)
            ]);
        }

        // --- Products ---
        $productsData = [
            [
                'title' => 'Shimla Apple, 1kg',
                'description' => 'The apple is a round, sweet or tart fruit, often enjoyed fresh or used in various culinary dishes.',
                'keywords' => 'Apple,Fruits,Fruitopia,Syau,',
                'category' => 'Fruits',
                'brand' => 'Frutiopia',
                'price' => 270.00,
                'images' => ['apple.jpg', 'green apples.jpg', 'moreApples.jpg', 'apple.jpg'],
            ],
            [
                'title' => 'Banana Malbok, 1dozen',
                'description' => 'Bananas are sweet, yellow fruits, widely enjoyed for their convenience and nutritional value.',
                'keywords' => 'Banana,Fruits,Fruitopia,Kera',
                'category' => 'Fruits',
                'brand' => 'Frutiopia',
                'price' => 165.00,
                'images' => ['banana1doz.jpg', 'banana2.jpg', 'banana.jpg', 'banana1.jpg'],
            ],
            [
                'title' => 'Bitter Gourd(Karela), 1kg',
                'description' => 'Bitter gourd is a green, bitter-tasting vegetable used in Asian cuisine, known for potential health benefits.',
                'keywords' => 'Bitter Gourd.Karela,Vegetables,VegiVista',
                'category' => 'Vegetables',
                'brand' => 'VegiVista',
                'price' => 120.00,
                'images' => ['bitterGourdimg.jpg', 'bittergourd.jpg', 'blackBittergourd.jpg', 'bitterGourdimg.jpg'],
            ],
            [
                'title' => 'Capsicum ,1kg',
                'description' => 'Capsicum, also known as bell pepper or sweet pepper, is a colorful and mildly flavored vegetable often used in cooking and salads.',
                'keywords' => 'Capsicum,Vegetables,VegiVista',
                'category' => 'Vegetables',
                'brand' => 'VegiVista',
                'price' => 100.00,
                'images' => ['greenCapsicum.png', 'capsicum.jpg', 'redCapsicum.jpg', 'greenCapsicum.png'],
            ],
            [
                'title' => 'Eggs(pack of 30)',
                'description' => 'Eggs are nutritious, versatile, and widely consumed food items',
                'keywords' => 'Eggs,Meat And Eggs,ABC Butchers,Aanda',
                'category' => 'Meat and Eggs',
                'brand' => 'ABC Butchers',
                'price' => 580.00,
                'images' => ['egg.jpg', 'aanda2.webp', 'aanda1.webp', 'egg.jpg'],
            ],
            [
                'title' => 'Tokla Tea Jar,500gm',
                'description' => 'Tea is a popular beverage made by steeping dried tea leaves in hot water, enjoyed for its diverse flavors and potential health benefits.',
                'keywords' => 'Tokla Tea,Tea,Tea And Coffee',
                'category' => 'Tea And Coffee',
                'brand' => 'Tokla Tea',
                'price' => 320.00,
                'images' => ['toklaTea500g.jpg', 'toklaTea500g.jpg', 'toklaTea500g.jpg', 'toklaTea500g.jpg'],
            ],
            [
                'title' => 'Himalayan Java Coffee - Everest Roast, 250 gms',
                'description' => 'Coffee is a beloved beverage made from roasted coffee beans, known for its rich, stimulating flavor and caffeine content.',
                'keywords' => 'Tea And Coffee,Himalayan Java Coffee,Coffee,HJC,Everest Roast',
                'category' => 'Tea And Coffee',
                'brand' => 'Himalayan Java Coffee',
                'price' => 825.00,
                'images' => ['HJCEverestRoast.jpg', 'coffeeBeans.jpg', 'coffeeLogo.jpg', 'evestRoast.png'],
            ],
            [
                'title' => 'Salad Tomato, 1Kg',
                'description' => ' Tomato is a red, juicy fruit widely used in cooking and salads, known for its fresh, slightly sweet taste.',
                'keywords' => 'Salad Tomato,Tomato,Vegetables,VegiVista,Tamatar',
                'category' => 'Vegetables',
                'brand' => 'VegiVista',
                'price' => 140.00,
                'images' => ['tomato.jpg', 'tomatoes.jpg', 'tomatoimg.jpg', 'tomato.jpg'],
            ],
            [
                'title' => 'DDC Standard Milk (Blue), 500ml',
                'description' => 'Milk is a white, nutrient-rich liquid produced by mammals, commonly consumed for its high calcium and protein content.',
                'keywords' => 'DDC Milk,Milk,Dairy Products,Dudh',
                'category' => 'Dairy Products',
                'brand' => 'DDC Milk',
                'price' => 50.00,
                'images' => ['ddcMilk.jpg', 'ddcMilk1.jpg', 'milkDDc.jpg', 'ddcMilk.jpg'],
            ],
            [
                'title' => 'Dalley Khursani, 1kg',
                'description' => ' Dalley Khursani, also known as "round chili" or "Nepali round chili," is a small, round, and extremely spicy chili pepper commonly used in Nepali cuisine to add heat and flavor to dishes.',
                'keywords' => 'Dalley Khursani,Chilli,Vegetables,VegiVista,Khursani',
                'category' => 'Vegetables',
                'brand' => 'VegiVista',
                'price' => 380.00,
                'images' => ['dalleKhursani.jpg', 'dalleKhursani2.jpg', 'dalleKhursani1.jpg', 'dalleKhursani2.jpg'],
            ],
            [
                'title' => 'Sugar, 1kg',
                'description' => ' Sugar is a sweet, crystalline substance, often derived from sugar cane or sugar beets, used to sweeten foods and beverages.',
                'keywords' => 'Sugar,Chini,Swetner,NepSweet',
                'category' => 'Sweetner',
                'brand' => 'NepSweet',
                'price' => 120.00,
                'images' => ['sugar.jpg', 'suagrImg.jpg', 'sugarrrrImg.jpg', 'sugar.jpg'],
            ],
            [
                'title' => 'Swastik Vanaspati Ghee, 1kg',
                'description' => 'Ghee is a type of clarified butter commonly used in Indian and Middle Eastern cuisines, known for its rich, nutty flavor and high cooking tolerance.',
                'keywords' => 'Swastik Vanaspati Ghee,Ghee,Dairy Products,Swastik',
                'category' => 'Dairy Products',
                'brand' => 'Swastik',
                'price' => 210.00,
                'images' => ['ghee.jpg', 'swastikGhee.webp', 'GHEE12345.jpg', 'swastikghee123.jpg'],
            ]
        ];

        foreach ($productsData as $data) {
            $product = Product::create([
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'description' => $data['description'],
                'keywords' => $data['keywords'],
                'price' => $data['price'],
                'stock_quantity' => 50,
                'status' => 'active',
                'category_id' => $categories[$data['category']]->id,
                'brand_id' => $brands[$data['brand']]->id,
            ]);

            foreach ($data['images'] as $index => $imageName) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => "products/migrated/{$imageName}",
                    'is_primary' => $index === 0
                ]);
            }
        }
    }
}
