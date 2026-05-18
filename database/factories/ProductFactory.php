<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $title = $this->faker->words(3, true);

        return [
            'title' => ucwords($title),
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->sentence(),
            'keywords' => $this->faker->words(5, true),
            'price' => $this->faker->randomFloat(2, 50, 2000),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'status' => 'active',
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
        ];
    }
}
