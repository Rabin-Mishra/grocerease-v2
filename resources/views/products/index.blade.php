@extends('layouts.app')

@section('title', 'All Products - GrocerEase')

@section('content')
<div class="container-fluid px-lg-5 px-md-4 px-3">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Products</li>
                </ol>
            </nav>
            <h2 class="fw-bold">All Products</h2>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Categories</h5>
                    <ul class="list-unstyled mb-0">
                        <li>
                            <a href="{{ route('products.index', array_merge(request()->except(['category_id', 'page']))) }}" 
                               class="text-decoration-none d-block py-1 {{ !request('category_id') ? 'text-success fw-bold' : 'text-dark' }}">
                               All Categories
                            </a>
                        </li>
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('products.index', array_merge(request()->except('page'), ['category_id' => $category->id])) }}" 
                                   class="text-decoration-none d-block py-1 {{ request('category_id') == $category->id ? 'text-success fw-bold' : 'text-dark' }}">
                                   {{ $category->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Brands</h5>
                    <ul class="list-unstyled mb-0">
                        <li>
                            <a href="{{ route('products.index', array_merge(request()->except(['brand_id', 'page']))) }}" 
                               class="text-decoration-none d-block py-1 {{ !request('brand_id') ? 'text-success fw-bold' : 'text-dark' }}">
                               All Brands
                            </a>
                        </li>
                        @foreach($brands as $brand)
                            <li>
                                <a href="{{ route('products.index', array_merge(request()->except('page'), ['brand_id' => $brand->id])) }}" 
                                   class="text-decoration-none d-block py-1 {{ request('brand_id') == $brand->id ? 'text-success fw-bold' : 'text-dark' }}">
                                   {{ $brand->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
                @forelse($products as $product)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            <a href="{{ route('products.show', $product->slug) }}">
                                <img src="{{ optional($product->primaryImage)->image_url ?? asset('images/placeholder.png') }}" class="card-img-top p-3" alt="{{ $product->title }}" style="height: 200px; object-fit: contain;">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title mb-1">
                                    <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none">{{ $product->title }}</a>
                                </h6>
                                <p class="text-success fw-bold mb-3">{{ $product->formatted_price }}</p>
                                
                                <div class="mt-auto">
                                    <form action="{{ url('/cart/add') }}" method="POST" class="d-grid gap-2">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-outline-success btn-sm" {{ !$product->is_in_stock ? 'disabled' : '' }}>
                                            <i class="fa-solid fa-cart-plus me-1"></i> Add to Cart
                                        </button>
                                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-light btn-sm text-decoration-none">
                                            View Details
                                        </a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info border-0 shadow-sm">
                            <i class="fa-solid fa-circle-info me-2"></i> No products found matching your criteria.
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
<style>
.product-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.product-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
</style>
@endsection
