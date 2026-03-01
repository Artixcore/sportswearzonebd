@extends('layouts.store')

@section('title', 'Contact - ' . config('app.name'))

@section('content')
<div class="container py-5">
    <h1 class="h4 mb-4">Contact Us</h1>
    <div class="row">
        <div class="col-lg-6 mb-4">
            <form method="POST" action="{{ route('contact.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4" required>{{ old('message') }}</textarea>
                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary">Send</button>
            </form>
        </div>
        <div class="col-lg-6">
            <h6 class="fw-bold">Get in touch</h6>
            <p class="text-muted small">Email: info@{{ parse_url(config('app.url'), PHP_URL_HOST) ?? 'example.com' }}</p>
        </div>
    </div>
</div>
@endsection
