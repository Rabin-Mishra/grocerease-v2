@extends('layouts.app')

@section('title', 'GrocerEase — Fresh Groceries Delivered')

@section('content')
<div class="container-fluid px-lg-5 px-md-4 px-3">
    <!-- Hero Section -->
    <div class="row align-items-center mb-5 py-5 bg-light rounded px-4">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4 fw-bold">Fresh Groceries, Delivered to Your Door</h1>
            <p class="lead text-muted mb-4">Quality produce from local Nepali farms and trusted brands.</p>
            <a href="{{ url('/products') }}" class="btn btn-success btn-lg px-4 me-2">Shop Now</a>
            <a href="{{ url('/products') }}" class="btn btn-outline-success btn-lg px-4">Browse Categories</a>
        </div>
    </div>

    <!-- Featured Products Section (Now First!) -->
    @if(isset($featuredProducts) && $featuredProducts->count() > 0)
    <div class="pt-4 mb-5">
        <h3 class="mb-4 fw-bold text-success text-center">Featured Products</h3>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach($featuredProducts as $product)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm product-card">
                    <a href="{{ route('products.show', $product->slug) }}">
                        <img src="{{ $product->primaryImage ? $product->primaryImage->image_url : asset('images/placeholder.png') }}" class="card-img-top p-3" alt="{{ $product->title }}" style="height: 200px; object-fit: contain;">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title mb-1">
                            <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none">{{ $product->title }}</a>
                        </h6>
                        <p class="text-success fw-bold mb-3">{{ $product->formatted_price }}</p>
                        
                        <div class="mt-auto">
                            <form action="{{ url('/cart/add') }}" method="POST" class="d-grid">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-outline-success btn-sm" {{ !$product->is_in_stock ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-cart-plus me-1"></i> Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Shop By Category Section (Second!) -->
    @if(isset($categories) && $categories->count() > 0)
    <div id="categories-section" class="pt-5 mb-5">
        <h3 class="mb-4 text-center fw-bold text-success">Shop By Category</h3>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
            @foreach($categories as $category)
            <div class="col">
                <a href="{{ route('products.index', ['category_id' => $category->id]) }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center category-card">
                        <div class="card-body d-flex flex-column justify-content-center py-4">
                            <i class="fa-solid fa-basket-shopping fs-1 text-success mb-3"></i>
                            <h5 class="card-title text-dark mb-1">{{ $category->title }}</h5>
                            <small class="text-muted">{{ $category->products_count ?? 0 }} Products</small>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Shop By Brand Section (Third!) -->
    @if(isset($brands) && $brands->count() > 0)
    <div id="brands-section" class="pt-5 mb-5">
        <h3 class="mb-4 text-center fw-bold text-success">Shop By Brand</h3>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3 justify-content-center">
            @foreach($brands as $brand)
            <div class="col">
                <a href="{{ route('products.index', ['brand_id' => $brand->id]) }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center brand-card py-3">
                        <div class="card-body py-2">
                            <i class="fa-solid fa-tag text-success mb-2 fs-4"></i>
                            <h6 class="card-title text-dark mb-1 fw-semibold">{{ $brand->title }}</h6>
                            <span class="badge bg-light text-success border border-success-subtle small">{{ $brand->products_count ?? 0 }} Products</span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
<style>
.product-card, .category-card, .brand-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.product-card:hover, .category-card:hover, .brand-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
</style>
@endsection
