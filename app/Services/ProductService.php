<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function getHomepageProducts(int $limit = 12): Collection
    {
        return Product::where('status', 'active')
            ->inRandomOrder()
            ->take($limit)
            ->with(['primaryImage', 'category'])
            ->get();
    }

    public function getAllProducts(array $filters = []): LengthAwarePaginator
    {
        $query = Product::where('status', 'active')->with('primaryImage');

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('keywords', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate(16)->withQueryString();
    }

    public function getProductBySlug(string $slug): Product
    {
        return Product::where('slug', $slug)
            ->where('status', 'active')
            ->with(['images', 'category', 'brand'])
            ->firstOrFail();
    }

    public function searchProducts(string $search): LengthAwarePaginator
    {
        return Product::where('status', 'active')
            ->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('keywords', 'like', '%' . $search . '%');
            })
            ->with('primaryImage')
            ->paginate(16)->withQueryString();
    }

    public function getFeaturedCategories(): Collection
    {
        return Category::withCount(['products' => function ($query) {
            $query->where('status', 'active');
        }])->having('products_count', '>', 0)->get();
    }
}
