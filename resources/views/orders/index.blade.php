@extends('layouts.app')

@section('title', 'My Orders - GrocerEase')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">My Orders</h2>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Order ID</th>
                            <th scope="col">Date</th>
                            <th scope="col">Total</th>
                            <th scope="col">Payment</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="ps-4 fw-bold">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>Rs. {{ number_format($order->total, 2) }}</td>
                            <td>
                                @if($order->payment_method === 'esewa')
                                    <span class="badge bg-success">eSewa</span>
                                @elseif($order->payment_method === 'khalti')
                                    <span class="badge" style="background-color: #5C2D91;">Khalti</span>
                                @else
                                    <span class="badge bg-secondary">COD</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $order->status_badge_color }}">{{ ucfirst($order->order_status) }}</span>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-success">View Details</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fa-solid fa-box-open text-muted mb-3" style="font-size: 3rem;"></i>
                                <h5 class="fw-bold mb-0">No orders found</h5>
                                <p class="text-muted">You haven't placed any orders yet.</p>
                                <a href="{{ route('products.index') }}" class="btn btn-success mt-2">Start Shopping</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($orders->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
