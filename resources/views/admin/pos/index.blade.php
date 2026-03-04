@extends('layouts.admin')

@section('title', 'POS')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-4">Point of Sale</h1>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
            <label class="block text-sm font-medium text-slate-700 mb-2">Search products (name or SKU)</label>
            <input type="text" id="pos-search" placeholder="Type to search..." class="w-full rounded border-slate-300 shadow-sm" autocomplete="off">
            <div id="pos-search-results" class="mt-2 border border-slate-200 rounded-md max-h-64 overflow-y-auto hidden"></div>
        </div>
        <div class="bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 font-medium">Cart</div>
            <div id="pos-cart" class="divide-y divide-slate-200">
                <p id="pos-cart-empty" class="px-4 py-6 text-slate-500 text-center">Cart is empty. Search and add products.</p>
            </div>
        </div>
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
            <label class="block text-sm font-medium text-slate-700 mb-2">Customer (optional)</label>
            <select id="pos-customer" class="w-full rounded border-slate-300 shadow-sm">
                <option value="">Walk-in</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} {{ $c->email ? '(' . $c->email . ')' : '' }}</option>
                @endforeach
            </select>
        </div>
        <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
            <label class="block text-sm font-medium text-slate-700 mb-2">Order discount</label>
            <input type="number" id="pos-order-discount" value="0" step="0.01" min="0" class="w-full rounded border-slate-300 shadow-sm">
        </div>
        <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
            <label class="block text-sm font-medium text-slate-700 mb-2">Payment method</label>
            <select id="pos-payment" class="w-full rounded border-slate-300 shadow-sm">
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="cod">COD</option>
            </select>
        </div>
        <div class="bg-white rounded-lg shadow border border-slate-200 p-4">
            <p class="flex justify-between text-lg font-semibold"><span>Total</span><span id="pos-total">৳0</span></p>
            <button type="button" id="pos-checkout" class="w-full mt-3 px-4 py-3 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 font-medium">Complete Sale</button>
        </div>
    </div>
</div>

<script>
const csrf = '{{ csrf_token() }}';
const checkoutUrl = '{{ route("admin.pos.checkout") }}';
let cart = [];

function renderCart() {
    const container = document.getElementById('pos-cart');
    const empty = document.getElementById('pos-cart-empty');
    if (cart.length === 0) {
        container.innerHTML = '<p id="pos-cart-empty" class="px-4 py-6 text-slate-500 text-center">Cart is empty. Search and add products.</p>';
        document.getElementById('pos-total').textContent = '৳0';
        return;
    }
    let html = '';
    let total = 0;
    cart.forEach((item, i) => {
        const lineTotal = item.price * item.quantity - (item.discount || 0);
        total += lineTotal;
        html += '<div class="px-4 py-2 flex items-center justify-between gap-2 border-b border-slate-100">';
        html += '<div class="flex-1 min-w-0"><p class="font-medium truncate">' + item.name + '</p><p class="text-sm text-slate-500">× ' + item.quantity + (item.discount ? ' · Disc ৳' + Number(item.discount).toFixed(0) : '') + '</p></div>';
        html += '<div class="flex items-center gap-2 flex-wrap">';
        html += '<label class="sr-only">Price</label><input type="number" min="0" step="0.01" value="' + Number(item.price) + '" class="w-20 rounded border-slate-300 text-sm pos-cart-price" data-index="' + i + '" title="Unit price" placeholder="Price">';
        html += '<input type="number" min="1" value="' + item.quantity + '" class="w-14 rounded border-slate-300 text-sm pos-cart-qty" data-index="' + i + '">';
        html += '<input type="number" min="0" step="0.01" value="' + (item.discount || 0) + '" class="w-16 rounded border-slate-300 text-sm pos-cart-discount" data-index="' + i + '" placeholder="Disc">';
        html += '<span class="font-medium w-20 text-right">৳' + lineTotal.toFixed(0) + '</span>';
        html += '<button type="button" class="pos-cart-remove text-red-600 hover:text-red-800" data-index="' + i + '">×</button>';
        html += '</div></div>';
    });
    const orderDiscount = parseFloat(document.getElementById('pos-order-discount').value) || 0;
    total -= orderDiscount;
    container.innerHTML = html;
    document.getElementById('pos-total').textContent = '৳' + Math.round(total);
    document.querySelectorAll('.pos-cart-price').forEach(el => el.addEventListener('change', function() { cart[this.dataset.index].price = parseFloat(this.value) || 0; renderCart(); }));
    document.querySelectorAll('.pos-cart-qty').forEach(el => el.addEventListener('change', function() { cart[this.dataset.index].quantity = parseInt(this.value, 10) || 1; renderCart(); }));
    document.querySelectorAll('.pos-cart-discount').forEach(el => el.addEventListener('change', function() { cart[this.dataset.index].discount = parseFloat(this.value) || 0; renderCart(); }));
    document.querySelectorAll('.pos-cart-remove').forEach(el => el.addEventListener('click', function() { cart.splice(parseInt(this.dataset.index, 10), 1); renderCart(); }));
}

let searchTimeout;
document.getElementById('pos-search').addEventListener('input', function() {
    const q = this.value.trim();
    const results = document.getElementById('pos-search-results');
    clearTimeout(searchTimeout);
    if (q.length < 2) { results.classList.add('hidden'); results.innerHTML = ''; return; }
    searchTimeout = setTimeout(() => {
        fetch('{{ route("admin.pos.searchProducts") }}?q=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(products => {
                if (products.length === 0) { results.innerHTML = '<p class="p-3 text-slate-500">No products found.</p>'; results.classList.remove('hidden'); return; }
                let html = '';
                products.forEach(p => {
                    if (p.variants && p.variants.length > 0) {
                        p.variants.forEach(v => {
                            const name = p.name + ' - ' + v.name;
                            const price = parseFloat(p.price) + parseFloat(v.price_adjustment || 0);
                            const sku = v.sku;
                            html += '<div class="p-3 border-b border-slate-100 hover:bg-slate-50 cursor-pointer flex justify-between items-center pos-add-item" data-product-id="' + p.id + '" data-variant-id="' + v.id + '" data-name="' + name.replace(/"/g, '&quot;') + '" data-price="' + price + '" data-sku="' + (sku || '') + '"><span>' + name + '</span><span>৳' + Math.round(price) + '</span></div>';
                        });
                    } else {
                        html += '<div class="p-3 border-b border-slate-100 hover:bg-slate-50 cursor-pointer flex justify-between items-center pos-add-item" data-product-id="' + p.id + '" data-variant-id="" data-name="' + p.name.replace(/"/g, '&quot;') + '" data-price="' + p.price + '" data-sku="' + (p.sku || '') + '"><span>' + p.name + '</span><span>৳' + Math.round(parseFloat(p.price)) + '</span></div>';
                    }
                });
                results.innerHTML = html;
                results.classList.remove('hidden');
                results.querySelectorAll('.pos-add-item').forEach(el => {
                    el.addEventListener('click', function() {
                        const existing = cart.find(x => x.product_id === parseInt(this.dataset.productId, 10) && String(x.product_variant_id || '') === String(this.dataset.variantId || ''));
                        if (existing) existing.quantity += 1;
                        else cart.push({ product_id: parseInt(this.dataset.productId, 10), product_variant_id: this.dataset.variantId ? parseInt(this.dataset.variantId, 10) : null, name: this.dataset.name, price: parseFloat(this.dataset.price), sku: this.dataset.sku, quantity: 1, discount: 0 });
                        renderCart();
                        document.getElementById('pos-search-results').classList.add('hidden');
                        document.getElementById('pos-search').value = '';
                    });
                });
            })
            .catch(() => { results.innerHTML = '<p class="p-3 text-red-500">Error searching.</p>'; results.classList.remove('hidden'); });
    }, 300);
});

document.getElementById('pos-checkout').addEventListener('click', function() {
    if (cart.length === 0) {
        if (typeof showAlert === 'function') showAlert('warning', 'Cart is empty', 'Add products before completing the sale.');
        else alert('Cart is empty.');
        return;
    }
    const btn = this;
    btn.disabled = true;
    const items = cart.map(i => ({
        product_id: i.product_id,
        product_variant_id: i.product_variant_id || null,
        quantity: i.quantity,
        price: i.price,
        discount: i.discount || 0
    }));
    fetch(checkoutUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({
            _token: csrf,
            items,
            customer_id: document.getElementById('pos-customer').value || null,
            payment_method: document.getElementById('pos-payment').value,
            order_discount: document.getElementById('pos-order-discount').value || 0,
            notes: ''
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.redirect) window.location.href = data.redirect;
        else {
            if (typeof showAlert === 'function') showAlert('error', 'Error', data.message || 'Something went wrong.');
            else alert(data.message || 'Error');
        }
    })
    .catch(() => {
        if (typeof showAlert === 'function') showAlert('error', 'Error', 'Network or server error. Please try again.');
        else alert('Error');
    })
    .finally(() => { btn.disabled = false; });
});
</script>
@endsection
