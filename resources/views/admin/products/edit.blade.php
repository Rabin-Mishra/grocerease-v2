@extends('layouts.admin')

@section('title', 'Edit Product - GrocerEase Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Edit Product: {{ Str::limit($product->title, 30) }}</h2>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-2"></i> Back</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0 mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Product Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $product->title) }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5" required>{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Brand <span class="text-danger">*</span></label>
                                <select name="brand_id" class="form-select" required>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Search Keywords</label>
                            <input type="text" name="keywords" class="form-control" value="{{ old('keywords', $product->keywords) }}">
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Pricing & Inventory</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Price (Rs.) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}" required min="0">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product->stock_quantity) }}" required min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Publishing</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Active (Visible)</option>
                                <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Inactive (Hidden)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">Update Product</button>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Existing Images</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-2">Check the box on an image to delete it upon updating.</p>
                        <div class="row g-2">
                            @foreach($product->images as $img)
                                <div class="col-6 position-relative">
                                    <div class="border rounded p-1 text-center bg-light">
                                        <img src="{{ $img->image_url }}" class="img-fluid rounded mb-2" style="height: 100px; object-fit: contain;">
                                        @if($img->is_primary)
                                            <span class="badge bg-success position-absolute top-0 start-0 m-2">Primary</span>
                                        @endif
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input me-2" type="checkbox" name="delete_images[]" value="{{ $img->id }}" id="del_{{ $img->id }}">
                                            <label class="form-check-label text-danger small" for="del_{{ $img->id }}">Delete</label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Add New Images</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-2">You can have a maximum of 4 images total.</p>
                        <div class="mb-3">
                            <input class="form-control" type="file" name="new_images[]" multiple accept="image/*" id="image-upload">
                        </div>
                        <div id="image-preview" class="d-flex flex-wrap gap-2 mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('image-upload').addEventListener('change', function(e) {
    const previewContainer = document.getElementById('image-preview');
    previewContainer.innerHTML = '';
    
    for (let i = 0; i < this.files.length; i++) {
        const file = this.files[i];
        if (!file.type.match('image.*')) continue;

        const reader = new FileReader();
        reader.onload = (function(theFile) {
            return function(e) {
                const imgWrap = document.createElement('div');
                imgWrap.className = 'border rounded p-1 bg-white';
                imgWrap.style.width = '70px';
                imgWrap.style.height = '70px';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'contain';
                
                imgWrap.appendChild(img);
                previewContainer.appendChild(imgWrap);
            };
        })(file);
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
