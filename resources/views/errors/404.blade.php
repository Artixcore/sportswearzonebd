@extends('layouts.app')

@section('title', 'Page Not Found - ' . config('app.name'))
@section('meta_description', 'The page you are looking for could not be found.')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-16 text-center sm:px-6 lg:px-8">
    <h1 class="text-6xl font-bold text-gray-900 sm:text-8xl">404</h1>
    <p class="mt-4 text-lg text-gray-600">Sorry, the page you are looking for could not be found.</p>
    <div class="mt-8 flex flex-wrap justify-center gap-4">
        <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-lg bg-accent px-6 py-3 text-base font-medium text-white hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">Go to Home</a>
        <a href="{{ route('shop.index') }}" class="inline-flex items-center justify-center rounded-lg border-2 border-gray-300 px-6 py-3 text-base font-medium text-gray-700 hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">Browse Shop</a>
    </div>
</div>
@endsection
