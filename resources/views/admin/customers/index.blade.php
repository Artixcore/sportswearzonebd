@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<h1 class="h4 mb-4">Customers</h1>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Registered</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $u)
                <tr>
                    <td>{{ $u->id }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->created_at->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $customers->links() }}
@endsection
