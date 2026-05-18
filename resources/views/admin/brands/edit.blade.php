@extends('layouts.admin')

@section('title', 'Edit Brand - GrocerEase Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Edit Brand</h2>
        <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-2"></i> Back</a>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $brand->title) }}" required>
                            @error('title')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Brand</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
