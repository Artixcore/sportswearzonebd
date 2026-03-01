@extends('layouts.admin')

@section('title', 'Add Customer')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Add Customer</h1>
<form action="{{ route('admin.customers.store') }}" method="POST" class="max-w-md space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Name *</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded border-slate-300 shadow-sm">
        @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded border-slate-300 shadow-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded border-slate-300 shadow-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
        <textarea name="notes" rows="3" class="w-full rounded border-slate-300 shadow-sm">{{ old('notes') }}</textarea>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Create Customer</button>
        <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300">Cancel</a>
    </div>
</form>
@endsection
