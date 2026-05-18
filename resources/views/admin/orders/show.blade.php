@extends('layouts.admin')

@section('title', 'Order Details - GrocerEase Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h2>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-2"></i> Back to Orders</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Items Ordered</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-4">Product</th>
                                    <th scope="col" class="text-center">Price</th>
                                    <th scope="col" class="text-center">Qty</th>
                                    <th scope="col" class="pe-4 text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ optional($item->product->primaryImage)->image_url ?? asset('images/placeholder.png') }}" class="rounded border p-1 me-3 bg-white" style="width: 50px; height: 50px; object-fit: contain;">
                                            <div>
                                                <div class="fw-bold">{{ $item->product_title }}</div>
                                                <div class="small text-muted">ID: {{ $item->product_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">Rs. {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="pe-4 text-end fw-bold">Rs. {{ number_format($item->line_total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Customer & Shipping -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2"><strong>Name:</strong> {{ $order->user->name }}</div>
                            <div class="mb-2"><strong>Email:</strong> <a href="mailto:{{ $order->user->email }}">{{ $order->user->email }}</a></div>
                            <div class="mb-2"><strong>Member Since:</strong> {{ $order->user->created_at->format('M Y') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0">Shipping Address</h5>
                        </div>
                        <div class="card-body">
                            @if($order->address)
                                <div class="mb-1 fw-bold">{{ $order->user->name }}</div>
                                <div class="mb-1">{{ $order->address->address_line1 }}</div>
                                <div>{{ $order->address->city }}, {{ $order->address->district }}</div>
                            @else
                                <span class="text-muted">No address provided.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Action Panel -->
            <div class="card border-0 shadow-sm mb-4 border-top border-3 border-primary">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Order Status</h5>
                    
                    <div id="status-alert" class="alert d-none small"></div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Current Status</label>
                        <select id="order-status-select" class="form-select fw-bold text-uppercase" data-order-id="{{ $order->id }}">
                            <option value="placed" {{ $order->order_status == 'placed' ? 'selected' : '' }}>Placed</option>
                            <option value="confirmed" {{ $order->order_status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="processing" {{ $order->order_status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="dispatched" {{ $order->order_status == 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                            <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    
                    <button id="update-status-btn" class="btn btn-primary w-100">Update Status</button>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">Financial Summary</h5>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal</span>
                        <span>Rs. {{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Shipping</span>
                        <span>{{ $order->shipping_fee == 0 ? 'Free' : 'Rs. ' . number_format($order->shipping_fee, 2) }}</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fs-5 fw-bold">Total</span>
                        <span class="fs-4 fw-bold text-success">Rs. {{ number_format($order->total, 2) }}</span>
                    </div>

                    <div class="bg-light p-3 rounded border">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-bold">Payment Method</span>
                            <span class="badge bg-dark text-uppercase">{{ $order->payment_method }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-bold">Payment Status</span>
                            <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-warning' }} text-uppercase">
                                {{ $order->payment_status }}
                            </span>
                        </div>
                        @if($order->payment)
                            <hr class="my-2">
                            <div class="small text-muted">
                                <strong>Txn ID:</strong> <br>
                                {{ $order->payment->transaction_id }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('update-status-btn').addEventListener('click', function() {
    const select = document.getElementById('order-status-select');
    const orderId = select.dataset.orderId;
    const newStatus = select.value;
    const btn = this;
    const alertBox = document.getElementById('status-alert');
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Updating...';
    alertBox.className = 'alert d-none small';

    fetch(`/admin/orders/${orderId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        alertBox.classList.remove('d-none');
        if (data.success) {
            alertBox.classList.add('alert-success');
            alertBox.innerHTML = '<i class="fa-solid fa-check-circle me-1"></i> ' + data.message;
        } else {
            alertBox.classList.add('alert-danger');
            alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> ' + (data.message || 'Error occurred.');
            // reload to reset dropdown if invalid
            setTimeout(() => window.location.reload(), 2000);
        }
    })
    .catch(error => {
        alertBox.classList.remove('d-none');
        alertBox.classList.add('alert-danger');
        alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> Network error occurred.';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Update Status';
    });
});
</script>
@endsection
