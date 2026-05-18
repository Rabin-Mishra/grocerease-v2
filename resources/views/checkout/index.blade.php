@extends('layouts.app')

@section('title', 'Checkout - GrocerEase')

@section('content')
<div class="container my-5">
    <h2 class="fw-bold mb-4">Checkout</h2>

    <form action="{{ route('checkout.placeOrder') }}" method="POST" id="checkout-form">
        @csrf
        <div class="row">
            <!-- Left: Checkout Form -->
            <div class="col-lg-7 mb-4">
                
                <!-- Errors -->
                @if($errors->any())
                    <div class="alert alert-danger shadow-sm border-0 mb-4">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Address Selection -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-location-dot me-2 text-success"></i> 1. Shipping Address</h5>
                    </div>
                    <div class="card-body">
                        @if($addresses->count() > 0)
                            <div class="mb-4">
                                @foreach($addresses as $address)
                                    <div class="form-check mb-2 p-3 border rounded">
                                        <input class="form-check-input ms-1" type="radio" name="address_id" id="address_{{ $address->id }}" value="{{ $address->id }}" {{ $loop->first ? 'checked' : '' }}>
                                        <label class="form-check-label ms-3 w-100 cursor-pointer" for="address_{{ $address->id }}">
                                            <strong>{{ $address->full_address }}</strong>
                                        </label>
                                    </div>
                                @endforeach
                                
                                <div class="form-check p-3 border rounded mt-3">
                                    <input class="form-check-input ms-1" type="radio" name="address_id" id="address_new" value="new">
                                    <label class="form-check-label ms-3 w-100 fw-bold cursor-pointer" for="address_new">
                                        Add new address
                                    </label>
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="address_id" value="new">
                        @endif

                        <!-- New Address Form -->
                        <div id="new-address-form" class="{{ $addresses->count() > 0 ? 'd-none' : '' }}">
                            <h6 class="fw-bold mb-3">Enter delivery details</h6>
                            <div class="mb-3">
                                <label for="address_line1" class="form-label">Address Line 1</label>
                                <input type="text" class="form-control" id="address_line1" name="address_line1" value="{{ old('address_line1') }}" placeholder="Street name, landmark">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" placeholder="Kathmandu">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="district" class="form-label">District</label>
                                    <input type="text" class="form-control" id="district" name="district" value="{{ old('district') }}" placeholder="Kathmandu">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Selection -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-credit-card me-2 text-success"></i> 2. Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="border rounded p-3 text-center w-100 cursor-pointer payment-method-card h-100">
                                    <input type="radio" name="payment_method" value="esewa" class="d-none">
                                    <h5 class="text-success fw-bold mb-2">eSewa</h5>
                                    <small class="text-muted">Pay securely via eSewa</small>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="border rounded p-3 text-center w-100 cursor-pointer payment-method-card h-100">
                                    <input type="radio" name="payment_method" value="khalti" class="d-none">
                                    <h5 class="text-purple fw-bold mb-2" style="color: #5C2D91;">Khalti</h5>
                                    <small class="text-muted">Pay securely via Khalti</small>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="border rounded p-3 text-center w-100 cursor-pointer payment-method-card h-100">
                                    <input type="radio" name="payment_method" value="cod" class="d-none" checked>
                                    <h5 class="text-dark fw-bold mb-2">Cash</h5>
                                    <small class="text-muted">Cash on Delivery</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h4 class="card-title fw-bold mb-4">Order Summary</h4>
                        
                        <div class="mb-4">
                            @php $hasOutOfStock = false; @endphp
                            @foreach($cart->items as $item)
                                @php 
                                    $isOut = !$item->product || $item->product->stock_quantity < $item->quantity;
                                    if ($isOut) $hasOutOfStock = true;
                                @endphp
                                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ optional($item->product->primaryImage)->image_url ?? asset('images/placeholder.png') }}" class="rounded me-2 border" style="width: 40px; height: 40px; object-fit: contain;">
                                        <div>
                                            <div class="fw-bold small">{{ Str::limit($item->product->title ?? 'Unknown', 25) }} <span class="text-muted">x{{ $item->quantity }}</span></div>
                                            @if($isOut)
                                                <div class="text-danger" style="font-size: 0.75rem;">Insufficient stock ({{ $item->product->stock_quantity ?? 0 }} available)</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="fw-bold small">Rs. {{ number_format(($item->product->price ?? $item->price) * $item->quantity, 2) }}</div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span>Rs. {{ number_format($subtotal, 2) }}</span>
                        </div>
                        
                        @php
                            $shipping = $subtotal > 2000 ? 0 : 150;
                            $total = $subtotal + $shipping;
                        @endphp
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Shipping</span>
                            <span>{{ $shipping == 0 ? 'Free' : 'Rs. ' . number_format($shipping, 2) }}</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-5 fw-bold">Total</span>
                            <span class="fs-4 fw-bold text-success">Rs. {{ number_format($total, 2) }}</span>
                        </div>
                        
                        @if($hasOutOfStock)
                            <div class="alert alert-danger p-2 small mb-3">
                                <i class="fa-solid fa-triangle-exclamation"></i> Please remove out-of-stock items before placing your order.
                            </div>
                        @endif

                        <button type="submit" class="btn btn-success w-100 btn-lg" {{ $hasOutOfStock ? 'disabled' : '' }}>
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Address toggle logic
    const addressRadios = document.querySelectorAll('input[name="address_id"]');
    const newAddressForm = document.getElementById('new-address-form');
    
    addressRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'new') {
                newAddressForm.classList.remove('d-none');
            } else {
                newAddressForm.classList.add('d-none');
            }
        });
    });

    // Payment method style logic
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.payment-method-card').forEach(card => {
                card.classList.remove('border-success', 'bg-success-subtle');
            });
            this.closest('.payment-method-card').classList.add('border-success', 'bg-success-subtle');
        });
        // Initial set
        if(radio.checked) {
            radio.closest('.payment-method-card').classList.add('border-success', 'bg-success-subtle');
        }
    });
});
</script>
@endsection
