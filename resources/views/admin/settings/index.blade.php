@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Settings</h1>
<form action="{{ route('admin.settings.update') }}" method="POST" class="max-w-xl space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Site name</label>
        <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" class="w-full rounded border-slate-300 shadow-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Meta Pixel ID</label>
        <input type="text" name="meta_pixel_id" value="{{ old('meta_pixel_id', $settings['meta_pixel_id'] ?? '') }}" class="w-full rounded border-slate-300 shadow-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Meta Access Token</label>
        <input type="text" name="meta_access_token" value="{{ old('meta_access_token', $settings['meta_access_token'] ?? '') }}" class="w-full rounded border-slate-300 shadow-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">SEO default title</label>
        <input type="text" name="seo_default_title" value="{{ old('seo_default_title', $settings['seo_default_title'] ?? '') }}" class="w-full rounded border-slate-300 shadow-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">SEO default description</label>
        <textarea name="seo_default_description" rows="2" class="w-full rounded border-slate-300 shadow-sm">{{ old('seo_default_description', $settings['seo_default_description'] ?? '') }}</textarea>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Save Settings</button>
    </div>
</form>
@endsection
