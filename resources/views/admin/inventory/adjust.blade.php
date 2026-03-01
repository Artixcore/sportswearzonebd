@extends('layouts.admin')

@section('title', 'Adjust Inventory')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Adjust Inventory</h1>
<form action="{{ route('admin.inventory.storeAdjust') }}" method="POST" class="max-w-md space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Product *</label>
        <select name="product_id" id="product_id" required class="w-full rounded border-slate-300 shadow-sm">
            <option value="">Select product</option>
            @foreach($products as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
        </select>
    </div>
    <div id="variant_wrap" style="display: none;">
        <label class="block text-sm font-medium text-slate-700 mb-1">Variant</label>
        <select name="product_variant_id" id="product_variant_id" class="w-full rounded border-slate-300 shadow-sm">
            <option value="">— Product-level stock —</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Type *</label>
        <select name="type" class="w-full rounded border-slate-300 shadow-sm">
            <option value="in">Stock In</option>
            <option value="out">Stock Out</option>
            <option value="adjustment">Adjustment</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Quantity *</label>
        <input type="number" name="quantity" value="{{ old('quantity') }}" required class="w-full rounded border-slate-300 shadow-sm" placeholder="Positive for in, negative for out">
        @error('quantity')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
        <textarea name="notes" rows="2" class="w-full rounded border-slate-300 shadow-sm">{{ old('notes') }}</textarea>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Apply</button>
        <a href="{{ route('admin.inventory.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300">Cancel</a>
    </div>
</form>

<script>
const variantsByProduct = @json($variantsByProduct);
document.getElementById('product_id').addEventListener('change', function() {
    const productId = this.value;
    const wrap = document.getElementById('variant_wrap');
    const varSel = document.getElementById('product_variant_id');
    varSel.innerHTML = '<option value="">— Product-level stock —</option>';
    if (productId && variantsByProduct[productId] && variantsByProduct[productId].length > 0) {
        wrap.style.display = 'block';
        variantsByProduct[productId].forEach(v => {
            varSel.innerHTML += '<option value="' + v.id + '">' + v.name + ' (Stock: ' + v.stock + ')</option>';
        });
    } else {
        wrap.style.display = 'none';
    }
});
</script>
@endsection
