@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Products</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product</a>
</div>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>SKU</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
                <tr>
                    <td>
                        @if($p->primaryImage)
                            <img src="{{ asset('storage/' . $p->primaryImage->path) }}" alt="" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->sku ?? '—' }}</td>
                    <td>৳{{ number_format($p->price, 0) }}</td>
                    <td>{{ $p->stock }}</td>
                    <td>
                        @if($p->is_active)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif
                    </td>
                    <td>
                        <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.products.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $products->links() }}
@endsection
