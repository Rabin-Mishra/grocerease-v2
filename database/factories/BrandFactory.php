<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->word();

        return [
            'title' => ucfirst($title),
            'slug' => Str::slug($title),
        ];
    }
}
