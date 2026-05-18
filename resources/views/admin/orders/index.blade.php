@extends('layouts.admin')

@section('title', 'Manage Orders - GrocerEase Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Orders</h2>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="placed" {{ request('status') == 'placed' ? 'selected' : '' }}>Placed</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="dispatched" {{ request('status') == 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-success w-100 mb-1" type="submit">Filter Orders</button>
                    @if(request()->hasAny(['status', 'date_from', 'date_to']))
                        <a href="{{ route('admin.orders.index') }}" class="text-danger small text-decoration-none d-block text-center mt-2">Clear Filters</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Order ID</th>
                            <th scope="col">Customer</th>
                            <th scope="col">Date</th>
                            <th scope="col">Total</th>
                            <th scope="col">Payment Method</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="ps-4 fw-bold">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div>{{ $order->user->name }}</div>
                                <div class="small text-muted">{{ $order->user->email }}</div>
                            </td>
                            <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                            <td>Rs. {{ number_format($order->total, 2) }}</td>
                            <td>
                                @if($order->payment_method === 'esewa')
                                    <span class="badge bg-success">eSewa</span>
                                @elseif($order->payment_method === 'khalti')
                                    <span class="badge" style="background-color: #5C2D91;">Khalti</span>
                                @else
                                    <span class="badge bg-secondary">COD</span>
                                @endif
                                <div class="small mt-1">
                                    <span class="{{ $order->payment_status === 'paid' ? 'text-success fw-bold' : 'text-warning' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $order->status_badge_color }}">{{ ucfirst($order->order_status) }}</span>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">Manage</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-box-open fs-1 mb-3"></i>
                                <h5>No orders found</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
