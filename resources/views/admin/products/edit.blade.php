@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Edit Product</h1>
<div class="mb-4">
    <a href="{{ route('admin.products.variants.index', $product) }}" class="text-sm text-slate-600 hover:text-slate-900">Manage Variants</a>
</div>
<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full rounded border-slate-300 shadow-sm">
                @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" class="w-full rounded border-slate-300 shadow-sm">
                @error('slug')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                <select name="category_id" class="w-full rounded border-slate-300 shadow-sm">
                    <option value="">— None —</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Short Description</label>
                <textarea name="short_description" rows="2" class="w-full rounded border-slate-300 shadow-sm">{{ old('short_description', $product->short_description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full rounded border-slate-300 shadow-sm">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Price *</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required class="w-full rounded border-slate-300 shadow-sm">
                @error('price')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Compare at price</label>
                <input type="number" step="0.01" name="compare_at_price" value="{{ old('compare_at_price', $product->compare_at_price) }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Cost price</label>
                <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Discount %</label>
                <input type="number" step="0.01" name="discount_percent" value="{{ old('discount_percent', $product->discount_percent) }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">SKU</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Stock</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Low stock threshold</label>
                <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-slate-300">
                <label for="is_active" class="text-sm text-slate-700">Active</label>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded border-slate-300">
                <label for="is_featured" class="text-sm text-slate-700">Featured</label>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Sort order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Main photo</label>
                @php
                    $mainImagePath = $product->main_image_path ?? $product->images->first()?->path;
                @endphp
                @if($mainImagePath)
                    <div class="mb-2">
                        <img id="current_main_preview" src="{{ asset('storage/' . $mainImagePath) }}" alt="Main" class="w-24 h-24 object-cover rounded border border-slate-200">
                    </div>
                @else
                    <div id="current_main_preview" class="hidden mb-2">
                        <img src="" alt="Main" class="w-24 h-24 object-cover rounded border border-slate-200">
                    </div>
                @endif
                <label class="block text-xs text-slate-500 mb-1">Replace main photo (optional)</label>
                <input type="file" name="main_image" id="edit_main_image" accept=".jpg,.jpeg,.png,.webp" class="w-full text-sm">
                <div id="edit_main_image_preview" class="mt-2 hidden">
                    <img src="" alt="New main preview" class="w-24 h-24 object-cover rounded border border-slate-200">
                </div>
                @error('main_image')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Gallery photos</label>
                <p class="text-xs text-slate-500 mb-1">Up to 4 total. Delete below or add more.</p>
                <div id="gallery_list" class="space-y-2 mb-2">
                    @foreach($product->images as $img)
                        <div class="flex items-center gap-2 gallery-item" data-image-id="{{ $img->id }}">
                            <img src="{{ asset('storage/' . $img->path) }}" alt="" class="w-14 h-14 object-cover rounded border border-slate-200">
                            <button type="button" class="gallery-delete-btn px-2 py-1 text-sm text-red-600 hover:text-red-800 border border-red-200 rounded hover:bg-red-50" data-image-id="{{ $img->id }}">Delete</button>
                        </div>
                    @endforeach
                </div>
                <label class="block text-xs text-slate-500 mb-1">Add more gallery photos</label>
                <input type="file" name="gallery_images[]" id="edit_gallery_images" multiple accept=".jpg,.jpeg,.png,.webp" class="w-full text-sm">
                <div id="edit_gallery_preview" class="mt-2 flex flex-wrap gap-2"></div>
                @error('gallery_images')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                @error('gallery_images.*')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Update Product</button>
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300">Cancel</a>
    </div>
</form>
@push('scripts')
<script>
(function() {
    var deleteImageUrlBase = {{ json_encode(route('admin.products.images.destroy', [$product, 0])) }};
    var csrfToken = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.addEventListener('DOMContentLoaded', function() {
        var editMainInput = document.getElementById('edit_main_image');
        var editMainPreview = document.getElementById('edit_main_image_preview');
        if (editMainInput && editMainPreview) {
            editMainInput.addEventListener('change', function() {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        editMainPreview.querySelector('img').src = e.target.result;
                        editMainPreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    editMainPreview.classList.add('hidden');
                }
            });
        }
        var editGalleryInput = document.getElementById('edit_gallery_images');
        var editGalleryPreview = document.getElementById('edit_gallery_preview');
        if (editGalleryInput && editGalleryPreview) {
            editGalleryInput.addEventListener('change', function() {
                editGalleryPreview.innerHTML = '';
                var files = Array.from(this.files);
                files.forEach(function(file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-16 h-16 object-cover rounded border border-slate-200';
                        img.alt = 'Preview';
                        editGalleryPreview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            });
        }

        document.querySelectorAll('.gallery-delete-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var imageId = this.getAttribute('data-image-id');
                var row = this.closest('.gallery-item');
                if (typeof showConfirm !== 'function') {
                    if (confirm('Remove this gallery image?')) {
                        doDelete(imageId, row);
                    }
                    return;
                }
                showConfirm('Remove gallery image?', 'This image will be deleted from the product.', function() {
                    doDelete(imageId, row);
                });
            });
        });

        function doDelete(imageId, rowEl) {
            var url = deleteImageUrlBase.replace(/\/0$/, '/' + imageId);
            var xhr = new XMLHttpRequest();
            xhr.open('DELETE', url);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken || '');
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    if (rowEl) rowEl.remove();
                    if (typeof showAlert === 'function') showAlert('success', 'Done', 'Gallery image deleted.');
                } else {
                    var msg = 'Failed to delete image.';
                    try {
                        var j = JSON.parse(xhr.responseText);
                        if (j.message) msg = j.message;
                    } catch (e) {}
                    if (typeof showAlert === 'function') showAlert('error', 'Error', msg);
                }
            };
            xhr.onerror = function() {
                if (typeof showAlert === 'function') showAlert('error', 'Error', 'Request failed.');
            };
            xhr.send();
        }
    });
})();
</script>
@endpush
@endsection
