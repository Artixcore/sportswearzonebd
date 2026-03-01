@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<h1 class="h4 mb-4">Settings</h1>
<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card mb-4">
        <div class="card-header">Site</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Site Name</label>
                <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings['site_name']) }}">
            </div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header">SEO Defaults</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Default Meta Title</label>
                <input type="text" name="seo_default_title" class="form-control" value="{{ old('seo_default_title', $settings['seo_default_title']) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Default Meta Description</label>
                <textarea name="seo_default_description" class="form-control" rows="2">{{ old('seo_default_description', $settings['seo_default_description']) }}</textarea>
            </div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header">Meta Ads (Facebook / Instagram)</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Facebook Pixel ID</label>
                <input type="text" name="meta_pixel_id" class="form-control" value="{{ old('meta_pixel_id', $settings['meta_pixel_id']) }}" placeholder="e.g. 123456789">
            </div>
            <div class="mb-3">
                <label class="form-label">Conversions API Access Token</label>
                <input type="text" name="meta_access_token" class="form-control" value="{{ old('meta_access_token', $settings['meta_access_token']) }}" placeholder="Optional">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Save Settings</button>
</form>
@endsection
