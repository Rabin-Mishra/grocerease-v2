@extends('layouts.app')

@section('title', 'Order Details - GrocerEase')

@section('content')
<div class="container my-5">
    
    @if(request()->has('placed'))
        <div class="alert alert-success border-0 shadow-sm p-4 text-center mb-4">
            <i class="fa-solid fa-circle-check text-success mb-3" style="font-size: 3rem;"></i>
            <h3 class="fw-bold mb-2">Order Confirmed!</h3>
            <p class="mb-0 text-muted">Thank you for your purchase. Your order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} has been placed successfully.</p>
        </div>
    @endif

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-success text-decoration-none">My Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <!-- Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Order Items</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($order->items as $item)
                        <li class="list-group-item p-4">
                            <div class="d-flex">
                                <img src="{{ optional($item->product->primaryImage)->image_url ?? asset('images/placeholder.png') }}" class="rounded border p-1 me-3" style="width: 80px; height: 80px; object-fit: contain;">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1">{{ $item->product_title }}</h6>
                                    <p class="text-muted small mb-0">Rs. {{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</p>
                                </div>
                                <div class="fw-bold text-end">
                                    Rs. {{ number_format($item->line_total, 2) }}
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Shipping Address</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1 fw-bold">{{ auth()->user()->name }}</p>
                    @if($order->address)
                        <p class="mb-1 text-muted">{{ $order->address->address_line1 }}</p>
                        <p class="mb-0 text-muted">{{ $order->address->city }}, {{ $order->address->district }}</p>
                    @else
                        <p class="mb-0 text-muted">Address information unavailable.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Order Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">Order Info</h5>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Order Date</span>
                        <span class="fw-bold">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Order Status</span>
                        <span class="badge bg-{{ $order->status_badge_color }}">{{ ucfirst($order->order_status) }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Payment Method</span>
                        <span class="fw-bold text-uppercase">{{ $order->payment_method }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Payment Status</span>
                        <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($order->payment_status) }}</span>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">Summary</h5>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal</span>
                        <span>Rs. {{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Shipping</span>
                        <span>{{ $order->shipping_fee == 0 ? 'Free' : 'Rs. ' . number_format($order->shipping_fee, 2) }}</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center mb-0">
                        <span class="fs-5 fw-bold">Total</span>
                        <span class="fs-4 fw-bold text-success">Rs. {{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
