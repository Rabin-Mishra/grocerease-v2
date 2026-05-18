@extends('layouts.app')

@section('title', $product->title . ' - GrocerEase')

@section('content')
<div class="container-fluid px-lg-5 px-md-4 px-3">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-success text-decoration-none">Products</a></li>
            @if($product->category)
            <li class="breadcrumb-item"><a href="{{ route('products.index', ['category_id' => $product->category_id]) }}" class="text-success text-decoration-none">{{ $product->category->title }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $product->title }}</li>
        </ol>
    </nav>

    <div class="row bg-white p-4 rounded shadow-sm mb-5">
        <!-- Images -->
        <div class="col-md-5 mb-4 mb-md-0">
            <div class="border rounded p-3 text-center mb-3">
                <img src="{{ optional($product->primaryImage)->image_url ?? asset('images/placeholder.png') }}" class="img-fluid" alt="{{ $product->title }}" style="max-height: 400px; object-fit: contain;" id="mainImage">
            </div>
            
            @if($product->images && $product->images->count() > 1)
            <div class="d-flex gap-2 overflow-auto py-2">
                @foreach($product->images as $image)
                <div class="border rounded p-1 cursor-pointer" onclick="document.getElementById('mainImage').src='{{ $image->image_url }}'" style="cursor: pointer; width: 80px; height: 80px;">
                    <img src="{{ $image->image_url }}" class="img-fluid w-100 h-100" style="object-fit: contain;" alt="Thumbnail">
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Details -->
        <div class="col-md-7">
            <div class="mb-2">
                @if($product->brand)
                    <span class="badge bg-secondary me-2">{{ $product->brand->title }}</span>
                @endif
                @if($product->category)
                    <span class="badge bg-success">{{ $product->category->title }}</span>
                @endif
            </div>
            
            <h1 class="fw-bold mb-3">{{ $product->title }}</h1>
            
            <div class="d-flex align-items-center mb-4">
                <h3 class="text-success fw-bold mb-0 me-3">{{ $product->formatted_price }}</h3>
                @if($product->is_in_stock)
                    <span class="badge bg-success-subtle text-success border border-success px-2 py-1"><i class="fa-solid fa-check-circle me-1"></i> In Stock ({{ $product->stock_quantity }})</span>
                @else
                    <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1"><i class="fa-solid fa-xmark-circle me-1"></i> Out of Stock</span>
                @endif
            </div>

            <div class="mb-4">
                <p class="text-muted">{{ $product->description }}</p>
            </div>

            <hr>

            <form action="{{ url('/cart/add') }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                <div class="row align-items-end mb-3">
                    <div class="col-auto">
                        <label for="quantity" class="form-label fw-bold">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock_quantity > 0 ? $product->stock_quantity : 1 }}" style="width: 100px;">
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-success btn-lg px-5" {{ !$product->is_in_stock ? 'disabled' : '' }}>
                            <i class="fa-solid fa-cart-shopping me-2"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Related Products -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div class="mb-5">
        <h4 class="fw-bold mb-4">Related Products</h4>
        <div class="row row-cols-2 row-cols-md-4 g-4">
            @foreach($relatedProducts as $related)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm product-card">
                    <a href="{{ route('products.show', $related->slug) }}">
                        <img src="{{ optional($related->primaryImage)->image_url ?? asset('images/placeholder.png') }}" class="card-img-top p-3" alt="{{ $related->title }}" style="height: 180px; object-fit: contain;">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title mb-1">
                            <a href="{{ route('products.show', $related->slug) }}" class="text-dark text-decoration-none">{{ $related->title }}</a>
                        </h6>
                        <p class="text-success fw-bold">{{ $related->formatted_price }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
<style>
.product-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.product-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
</style>
@endsection
