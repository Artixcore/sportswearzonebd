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
                <div class="space-y-2">
                    <div>
                        <label class="block text-xs text-slate-500 mb-0.5">Parent category</label>
                        <select id="parent_category_id" class="w-full rounded border-slate-300 shadow-sm">
                            <option value="">— None —</option>
                            @foreach($rootCategories as $c)
                                <option value="{{ $c->id }}" {{ $selectedParentId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-0.5">Subcategory</label>
                        <select id="subcategory_id" class="w-full rounded border-slate-300 shadow-sm">
                            <option value="">— None —</option>
                        </select>
                    </div>
                    <input type="hidden" name="category_id" id="category_id" value="{{ old('category_id', $product->category_id) }}">
                </div>
                @error('category_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Size type</label>
                <select name="size_type" class="w-full rounded border-slate-300 shadow-sm">
                    <option value="standard" {{ old('size_type', $product->size_type ?? 'standard') === 'standard' ? 'selected' : '' }}>Standard (S, M, L, XL, XXL)</option>
                    <option value="numeric_panjabi" {{ old('size_type', $product->size_type) === 'numeric_panjabi' ? 'selected' : '' }}>Panjabi (40, 42, 44)</option>
                </select>
                <p class="text-xs text-slate-500 mt-0.5">Determines which sizes are available for this product and its variants.</p>
                @error('size_type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
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
                    $hasMainImagePath = !empty($product->main_image_path);
                @endphp
                @if($mainImagePath)
                    <div id="main_photo_container" class="mb-2">
                        <img id="current_main_preview" src="{{ storage_asset($mainImagePath) }}" alt="Main" class="w-24 h-24 object-cover rounded border border-slate-200">
                        @if($hasMainImagePath)
                            <button type="button" id="remove_main_photo_btn" class="mt-1 block text-sm text-red-600 hover:text-red-800">Remove main photo</button>
                        @endif
                    </div>
                @else
                    <div id="main_photo_container" class="hidden mb-2">
                        <img id="current_main_preview" src="" alt="Main" class="w-24 h-24 object-cover rounded border border-slate-200">
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
                            <img src="{{ storage_asset($img->path) }}" alt="" class="w-14 h-14 object-cover rounded border border-slate-200">
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
    <div class="mt-6 border-t border-slate-200 pt-6">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">SEO (optional)</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Meta title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}" maxlength="60" placeholder="Recommended max 60 characters" class="w-full rounded border-slate-300 shadow-sm">
                <p class="text-xs text-slate-500 mt-0.5">Recommended max 60 characters for search results.</p>
                @error('meta_title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Meta description</label>
                <textarea name="meta_description" rows="2" maxlength="160" placeholder="Recommended max 160 characters" class="w-full rounded border-slate-300 shadow-sm">{{ old('meta_description', $product->meta_description) }}</textarea>
                <p class="text-xs text-slate-500 mt-0.5">Recommended max 160 characters.</p>
                @error('meta_description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Meta keywords</label>
                <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $product->meta_keywords) }}" placeholder="Comma-separated keywords" class="w-full rounded border-slate-300 shadow-sm">
                @error('meta_keywords')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
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
    var subcategoriesByParentId = @json($subcategoriesByParentId);
    var selectedSubcategoryId = @json($selectedSubcategoryId);
    var parentSelect = document.getElementById('parent_category_id');
    var subcategorySelect = document.getElementById('subcategory_id');
    var categoryHidden = document.getElementById('category_id');

    function fillSubcategories(parentId) {
        subcategorySelect.innerHTML = '<option value="">— None —</option>';
        if (parentId && subcategoriesByParentId[parentId]) {
            subcategoriesByParentId[parentId].forEach(function(sub) {
                var opt = document.createElement('option');
                opt.value = sub.id;
                opt.textContent = sub.name;
                if (selectedSubcategoryId && sub.id == selectedSubcategoryId) opt.selected = true;
                subcategorySelect.appendChild(opt);
            });
        }
        syncCategoryId();
    }

    function syncCategoryId() {
        var subVal = subcategorySelect.value;
        var parentVal = parentSelect.value;
        categoryHidden.value = subVal || parentVal || '';
    }

    if (parentSelect) {
        fillSubcategories(parentSelect.value);
        parentSelect.addEventListener('change', function() {
            selectedSubcategoryId = null;
            fillSubcategories(this.value);
        });
    }
    if (subcategorySelect) {
        subcategorySelect.addEventListener('change', syncCategoryId);
    }

    var deleteImageUrlBase = {{ json_encode(route('admin.products.images.destroy', [$product, 0])) }};
    var removeMainImageUrl = {{ json_encode(route('admin.products.main-image.destroy', [$product])) }};
    var csrfToken = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function showAlertSafe(type, title, text) {
        if (typeof showAlert === 'function') {
            showAlert(type, title, text);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: type === 'error' ? 'error' : 'success', title: title, text: text });
        } else {
            alert(title + ': ' + text);
        }
    }

    function showConfirmSafe(title, text, onConfirm) {
        if (typeof showConfirm === 'function') {
            showConfirm(title, text, onConfirm);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({ title: title, text: text, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Yes, remove' }).then(function(r) {
                if (r.isConfirmed && onConfirm) onConfirm();
            });
        } else {
            if (confirm(title + ' ' + text)) onConfirm();
        }
    }

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
                var self = this;
                showConfirmSafe('Remove gallery image?', 'This image will be deleted from the product.', function() {
                    doDelete(imageId, row, self);
                });
            });
        });

        function doDelete(imageId, rowEl, btnEl) {
            if (btnEl) {
                btnEl.disabled = true;
                btnEl.textContent = 'Deleting…';
            }
            var url = deleteImageUrlBase.replace(/\/0$/, '/' + imageId);
            var xhr = new XMLHttpRequest();
            xhr.open('DELETE', url);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken || '');
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    if (rowEl) rowEl.remove();
                    showAlertSafe('success', 'Done', 'Gallery image deleted.');
                } else {
                    var msg = 'Failed to delete image.';
                    try {
                        var j = JSON.parse(xhr.responseText);
                        if (j.message) msg = j.message;
                    } catch (e) {}
                    showAlertSafe('error', 'Error', msg);
                    if (btnEl) {
                        btnEl.disabled = false;
                        btnEl.textContent = 'Delete';
                    }
                }
            };
            xhr.onerror = function() {
                showAlertSafe('error', 'Error', 'Request failed. Please try again.');
                if (btnEl) {
                    btnEl.disabled = false;
                    btnEl.textContent = 'Delete';
                }
            };
            xhr.send();
        }

        var removeMainBtn = document.getElementById('remove_main_photo_btn');
        if (removeMainBtn) {
            removeMainBtn.addEventListener('click', function() {
                var self = this;
                showConfirmSafe('Remove main photo?', 'The main product image will be removed.', function() {
                    self.disabled = true;
                    self.textContent = 'Removing…';
                    var xhr = new XMLHttpRequest();
                    xhr.open('DELETE', removeMainImageUrl);
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken || '');
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.onload = function() {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            var container = document.getElementById('main_photo_container');
                            if (container) {
                                container.querySelector('img').src = '';
                                container.classList.add('hidden');
                                var removeBtn = document.getElementById('remove_main_photo_btn');
                                if (removeBtn) removeBtn.remove();
                            }
                            showAlertSafe('success', 'Done', 'Main photo removed.');
                        } else {
                            var msg = 'Failed to remove main photo.';
                            try {
                                var j = JSON.parse(xhr.responseText);
                                if (j.message) msg = j.message;
                            } catch (e) {}
                            showAlertSafe('error', 'Error', msg);
                            self.disabled = false;
                            self.textContent = 'Remove main photo';
                        }
                    };
                    xhr.onerror = function() {
                        showAlertSafe('error', 'Error', 'Request failed. Please try again.');
                        self.disabled = false;
                        self.textContent = 'Remove main photo';
                    };
                    xhr.send();
                });
            });
        }
    });
})();
</script>
@endpush
@endsection
