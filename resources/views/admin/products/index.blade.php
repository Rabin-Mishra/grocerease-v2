@extends('layouts.admin')

@section('title', 'Manage Products - GrocerEase Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Products</h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-success"><i class="fa-solid fa-plus me-2"></i> Add Product</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning border-0 shadow-sm mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('warning') }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" name="search" value="{{ request('search') }}" placeholder="Search products...">
                    <button class="btn btn-success" type="submit">Search</button>
                    @if(request('search'))
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Clear</a>
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
                            <th scope="col" class="ps-4" style="width: 80px;">Image</th>
                            <th scope="col">Title</th>
                            <th scope="col">Category & Brand</th>
                            <th scope="col">Price</th>
                            <th scope="col">Stock</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td class="ps-4">
                                <img src="{{ optional($product->primaryImage)->image_url ?? asset('images/placeholder.png') }}" class="rounded border bg-white p-1" style="width: 50px; height: 50px; object-fit: contain;">
                            </td>
                            <td>
                                <div class="fw-bold">{{ Str::limit($product->title, 40) }}</div>
                                <div class="small text-muted">{{ $product->slug }}</div>
                            </td>
                            <td>
                                <div class="small"><i class="fa-solid fa-folder me-1 text-muted"></i> {{ $product->category->title ?? 'N/A' }}</div>
                                <div class="small"><i class="fa-solid fa-tag me-1 text-muted"></i> {{ $product->brand->title ?? 'N/A' }}</div>
                            </td>
                            <td class="fw-bold">Rs. {{ number_format($product->price, 2) }}</td>
                            <td>
                                @if($product->stock_quantity < 5)
                                    <span class="badge bg-danger rounded-pill">{{ $product->stock_quantity }}</span>
                                @else
                                    <span class="badge bg-success rounded-pill">{{ $product->stock_quantity }}</span>
                                @endif
                            </td>
                            <td>
                                @if($product->status === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Inactive</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-box-open fs-1 mb-3"></i>
                                <h5>No products found</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
