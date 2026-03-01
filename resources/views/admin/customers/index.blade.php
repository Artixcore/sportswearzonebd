@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-semibold text-slate-800">Customers</h1>
    <a href="{{ route('admin.customers.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Add Customer</a>
</div>
<form method="GET" class="mb-4">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone..." class="rounded border-slate-300 shadow-sm w-64">
    <button type="submit" class="ml-2 px-3 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300">Search</button>
</form>
<div class="bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Name</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Email</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Phone</th>
                <th class="px-4 py-2 w-24"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @foreach($customers as $c)
                <tr>
                    <td class="px-4 py-2 font-medium"><a href="{{ route('admin.customers.show', $c) }}" class="text-slate-800 hover:text-emerald-600">{{ $c->name }}</a></td>
                    <td class="px-4 py-2 text-sm text-slate-600">{{ $c->email ?? '—' }}</td>
                    <td class="px-4 py-2 text-sm text-slate-600">{{ $c->phone ?? '—' }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.customers.show', $c) }}" class="text-sm text-slate-600 hover:text-slate-900">View</a>
                        <a href="{{ route('admin.customers.edit', $c) }}" class="text-sm text-slate-600 hover:text-slate-900 ml-2">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-200">{{ $customers->withQueryString()->links() }}</div>
</div>
@endsection
