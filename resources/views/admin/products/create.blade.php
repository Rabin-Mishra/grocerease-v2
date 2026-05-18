@extends('layouts.admin')

@section('title', 'Add Product - GrocerEase Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Add Product</h2>
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

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Product Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select Category...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Brand <span class="text-danger">*</span></label>
                                <select name="brand_id" class="form-select" required>
                                    <option value="">Select Brand...</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Search Keywords</label>
                            <input type="text" name="keywords" class="form-control" value="{{ old('keywords') }}" placeholder="organic, fresh, local (comma separated)">
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
                                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required min="0">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', 10) }}" required min="0">
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
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active (Visible)</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive (Hidden)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-lg">Create Product</button>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Images <span class="text-danger">*</span></h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info small py-2">
                            <i class="fa-solid fa-circle-info me-1"></i> First image will be the primary thumbnail. Max 4 images.
                        </div>
                        <div class="mb-3">
                            <input class="form-control" type="file" name="images[]" multiple accept="image/*" required id="image-upload">
                        </div>
                        <!-- Preview container -->
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
    
    if (this.files.length > 4) {
        alert('You can only upload a maximum of 4 images.');
        this.value = '';
        return;
    }

    for (let i = 0; i < this.files.length; i++) {
        const file = this.files[i];
        if (!file.type.match('image.*')) continue;

        const reader = new FileReader();
        reader.onload = (function(theFile, index) {
            return function(e) {
                const imgWrap = document.createElement('div');
                imgWrap.className = 'position-relative border rounded p-1 bg-white';
                imgWrap.style.width = '70px';
                imgWrap.style.height = '70px';
                
                if (index === 0) {
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-success position-absolute top-0 start-50 translate-middle-x';
                    badge.style.fontSize = '0.5rem';
                    badge.textContent = 'Primary';
                    imgWrap.appendChild(badge);
                }

                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'contain';
                
                imgWrap.appendChild(img);
                previewContainer.appendChild(imgWrap);
            };
        })(file, i);
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
