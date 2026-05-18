@extends('layouts.app')

@section('title', 'Shopping Cart - GrocerEase')

@section('content')
<div class="container my-5">
    <h2 class="fw-bold mb-4">Shopping Cart</h2>

    @if($cart && $cart->items->count() > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="ps-4">Product</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Total</th>
                                        <th scope="col" class="pe-4 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart->items as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ optional($item->product->primaryImage)->image_url ?? asset('images/placeholder.png') }}" class="rounded me-3 border bg-white p-1" style="width: 60px; height: 60px; object-fit: contain;" alt="{{ $item->product->title ?? 'Product' }}">
                                                <div>
                                                    <a href="{{ $item->product ? route('products.show', $item->product->slug) : '#' }}" class="text-dark text-decoration-none fw-bold">
                                                        {{ $item->product->title ?? 'Unknown Product' }}
                                                    </a>
                                                    @if(!$item->product || $item->product->stock_quantity == 0)
                                                        <div class="text-danger small"><i class="fa-solid fa-triangle-exclamation me-1"></i> Out of stock</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $item->product ? $item->product->formatted_price : 'Rs. ' . number_format($item->price, 2) }}</td>
                                        <td>
                                            <form action="{{ route('cart.update') }}" method="POST" class="d-flex align-items-center">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock_quantity ?? 99 }}" class="form-control form-control-sm text-center" style="width: 65px;">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary ms-2" title="Update Quantity">
                                                    <i class="fa-solid fa-arrows-rotate"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="fw-bold text-success">
                                            Rs. {{ number_format(($item->product->price ?? $item->price) * $item->quantity, 2) }}
                                        </td>
                                        <td class="pe-4 text-end">
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST" onsubmit="return confirm('Remove this item from your cart?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title fw-bold mb-4">Cart Summary</h4>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold">Rs. {{ number_format($subtotal, 2) }}</span>
                        </div>
                        
                        @php
                            $shipping = $subtotal > 2000 ? 0 : 150;
                            $total = $subtotal + $shipping;
                        @endphp
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Shipping</span>
                            <span class="fw-bold">
                                @if($shipping == 0)
                                    <span class="text-success">Free</span>
                                @else
                                    Rs. {{ number_format($shipping, 2) }}
                                @endif
                            </span>
                        </div>
                        
                        @if($shipping > 0)
                        <div class="alert alert-info py-2 small mb-4">
                            <i class="fa-solid fa-circle-info me-1"></i> Add <strong>Rs. {{ number_format(2000 - $subtotal, 2) }}</strong> more for free delivery!
                        </div>
                        @else
                        <div class="alert alert-success py-2 small mb-4">
                            <i class="fa-solid fa-check-circle me-1"></i> You qualify for free delivery!
                        </div>
                        @endif
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-5 fw-bold">Total</span>
                            <span class="fs-4 fw-bold text-success">Rs. {{ number_format($total, 2) }}</span>
                        </div>
                        
                        @if(auth()->check())
                            <a href="{{ route('checkout.index') }}" class="btn btn-success w-100 btn-lg mb-2">Proceed to Checkout</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary w-100 btn-lg mb-2">Please login to checkout</a>
                        @endif
                        
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-5 text-center">
                    <div class="card-body py-5">
                        <i class="fa-solid fa-cart-shopping text-muted mb-4" style="font-size: 5rem;"></i>
                        <h3 class="fw-bold mb-3">Your cart is empty</h3>
                        <p class="text-muted mb-4 fs-5">Looks like you haven't added anything to your cart yet.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-success btn-lg px-5">Start Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
