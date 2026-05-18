@extends('layouts.app')

@section('title', 'Search Results for "' . $query . '" - GrocerEase')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Search Results</h2>
            <p class="text-muted">Showing {{ $products->total() }} results for "<span class="text-dark fw-bold">{{ $query }}</span>"</p>
        </div>
    </div>

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
            <div class="col-12 w-100">
                <div class="alert alert-warning border-0 shadow-sm p-4 text-center">
                    <i class="fa-solid fa-magnifying-glass fs-1 text-warning mb-3"></i>
                    <h4 class="alert-heading fw-bold">No results found</h4>
                    <p class="mb-0">We couldn't find any products matching your search criteria. Please try a different keyword.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-success mt-3">Browse All Products</a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
    <div class="d-flex justify-content-center">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
<style>
.product-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.product-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
</style>
@endsection
