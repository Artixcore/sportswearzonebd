@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Add Product</h1>
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded border-slate-300 shadow-sm">
                @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Slug</label>
                <input type="text" name="slug" value="{{ old('slug') }}" placeholder="auto-generated if empty" class="w-full rounded border-slate-300 shadow-sm">
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
                    <input type="hidden" name="category_id" id="category_id" value="{{ $selectedSubcategoryId ?? $selectedParentId ?? old('category_id') }}">
                </div>
                @error('category_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Size type</label>
                <select name="size_type" class="w-full rounded border-slate-300 shadow-sm">
                    <option value="standard" {{ old('size_type', 'standard') === 'standard' ? 'selected' : '' }}>Standard (S, M, L, XL, XXL)</option>
                    <option value="numeric_panjabi" {{ old('size_type') === 'numeric_panjabi' ? 'selected' : '' }}>Panjabi (40, 42, 44)</option>
                </select>
                <p class="text-xs text-slate-500 mt-0.5">Determines which sizes are available for this product and its variants.</p>
                @error('size_type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Short Description</label>
                <textarea name="short_description" rows="2" class="w-full rounded border-slate-300 shadow-sm">{{ old('short_description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full rounded border-slate-300 shadow-sm">{{ old('description') }}</textarea>
            </div>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Price *</label>
                <input type="number" step="0.01" name="price" value="{{ old('price') }}" required class="w-full rounded border-slate-300 shadow-sm">
                @error('price')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Compare at price</label>
                <input type="number" step="0.01" name="compare_at_price" value="{{ old('compare_at_price') }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Cost price</label>
                <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price') }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Discount %</label>
                <input type="number" step="0.01" name="discount_percent" value="{{ old('discount_percent') }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">SKU</label>
                <input type="text" name="sku" value="{{ old('sku') }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Stock</label>
                <input type="number" name="stock" value="{{ old('stock', 0) }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Low stock threshold</label>
                <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 5) }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300">
                <label for="is_active" class="text-sm text-slate-700">Active</label>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-slate-300">
                <label for="is_featured" class="text-sm text-slate-700">Featured</label>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Sort order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Main photo *</label>
                <input type="file" name="main_image" id="main_image" accept=".jpg,.jpeg,.png,.webp" class="w-full text-sm">
                <p class="text-xs text-slate-500 mt-0.5">JPG, PNG or WebP, max 2MB</p>
                <div id="main_image_preview" class="mt-2 hidden">
                    <img src="" alt="Main preview" class="w-24 h-24 object-cover rounded border border-slate-200">
                </div>
                @error('main_image')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Gallery photos (optional)</label>
                <input type="file" name="gallery_images[]" id="gallery_images" multiple accept=".jpg,.jpeg,.png,.webp" class="w-full text-sm">
                <p class="text-xs text-slate-500 mt-0.5">Up to 4 images, optional. JPG, PNG or WebP, max 2MB each.</p>
                <div id="gallery_preview" class="mt-2 flex flex-wrap gap-2"></div>
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
                <input type="text" name="meta_title" value="{{ old('meta_title') }}" maxlength="60" placeholder="Recommended max 60 characters" class="w-full rounded border-slate-300 shadow-sm">
                <p class="text-xs text-slate-500 mt-0.5">Recommended max 60 characters for search results.</p>
                @error('meta_title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Meta description</label>
                <textarea name="meta_description" rows="2" maxlength="160" placeholder="Recommended max 160 characters" class="w-full rounded border-slate-300 shadow-sm">{{ old('meta_description') }}</textarea>
                <p class="text-xs text-slate-500 mt-0.5">Recommended max 160 characters.</p>
                @error('meta_description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Meta keywords</label>
                <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" placeholder="Comma-separated keywords" class="w-full rounded border-slate-300 shadow-sm">
                @error('meta_keywords')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Create Product</button>
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300">Cancel</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    var mainInput = document.getElementById('main_image');
    var mainPreview = document.getElementById('main_image_preview');
    if (mainInput && mainPreview) {
        mainInput.addEventListener('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    mainPreview.querySelector('img').src = e.target.result;
                    mainPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                mainPreview.classList.add('hidden');
            }
        });
    }
    var galleryInput = document.getElementById('gallery_images');
    var galleryPreview = document.getElementById('gallery_preview');
    if (galleryInput && galleryPreview) {
        galleryInput.addEventListener('change', function() {
            galleryPreview.innerHTML = '';
            var files = Array.from(this.files).slice(0, 4);
            files.forEach(function(file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-16 h-16 object-cover rounded border border-slate-200';
                    img.alt = 'Gallery preview';
                    galleryPreview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    }
});
</script>
@endpush
