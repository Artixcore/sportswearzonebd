@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Settings</h1>

@if(session('success'))
    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 text-sm" role="alert">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-800 text-sm" role="alert">{{ session('error') }}</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success') && isset($settings))
    if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'success', title: 'Saved', text: @json(session('success')), timer: 2500, showConfirmButton: false });
    }
    @endif
});
</script>
@endpush

<form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" class="max-w-2xl space-y-8">
    @csrf
    @method('PUT')

    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">General</h2>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Site name</label>
            <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" class="w-full rounded border-slate-300 shadow-sm">
        </div>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">SEO defaults</h2>
        <p class="text-sm text-slate-600 mb-4">Used when a page does not set its own meta.</p>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Default meta title</label>
                <input type="text" name="seo_default_title" value="{{ old('seo_default_title', $settings['seo_default_title'] ?? '') }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Default meta description</label>
                <textarea name="seo_default_description" rows="2" class="w-full rounded border-slate-300 shadow-sm">{{ old('seo_default_description', $settings['seo_default_description'] ?? '') }}</textarea>
                <p class="text-xs text-slate-500 mt-0.5">Recommended max 160 characters.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Default meta keywords</label>
                <input type="text" name="seo_default_keywords" value="{{ old('seo_default_keywords', $settings['seo_default_keywords'] ?? '') }}" placeholder="Comma-separated" class="w-full rounded border-slate-300 shadow-sm">
            </div>
        </div>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Tracking</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Google Tag Manager ID</label>
                <input type="text" name="gtm_id" value="{{ old('gtm_id', $settings['gtm_id'] ?? '') }}" placeholder="GTM-XXXXXXX" class="w-full rounded border-slate-300 shadow-sm">
                <p class="text-xs text-slate-500 mt-0.5">Format: GTM-XXXXXXX</p>
                @error('gtm_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Google Analytics Measurement ID</label>
                <input type="text" name="ga_measurement_id" value="{{ old('ga_measurement_id', $settings['ga_measurement_id'] ?? '') }}" placeholder="G-XXXXXXXXXX" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Meta Pixel ID</label>
                <input type="text" name="meta_pixel_id" value="{{ old('meta_pixel_id', $settings['meta_pixel_id'] ?? '') }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Meta Access Token</label>
                <input type="text" name="meta_access_token" value="{{ old('meta_access_token', $settings['meta_access_token'] ?? '') }}" class="w-full rounded border-slate-300 shadow-sm">
            </div>
        </div>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Scripts & verification</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Header scripts</label>
                <textarea name="header_scripts" rows="4" placeholder="Injected into &lt;head&gt;" class="w-full rounded border-slate-300 shadow-sm font-mono text-sm">{{ old('header_scripts', $settings['header_scripts'] ?? '') }}</textarea>
                <p class="text-xs text-slate-500 mt-0.5">Scripts or meta tags injected into &lt;head&gt;.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Footer scripts</label>
                <textarea name="footer_scripts" rows="4" placeholder="Injected before &lt;/body&gt;" class="w-full rounded border-slate-300 shadow-sm font-mono text-sm">{{ old('footer_scripts', $settings['footer_scripts'] ?? '') }}</textarea>
                <p class="text-xs text-slate-500 mt-0.5">Injected before closing &lt;/body&gt;.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Meta verification / custom head</label>
                <textarea name="meta_verification_scripts" rows="3" placeholder="e.g. Google Search Console, Facebook domain verification" class="w-full rounded border-slate-300 shadow-sm font-mono text-sm">{{ old('meta_verification_scripts', $settings['meta_verification_scripts'] ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Save Settings</button>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300">Cancel</a>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('settings-form');
    if (form && typeof Swal !== 'undefined') {
        form.addEventListener('submit', function() {
            Swal.fire({ icon: 'info', title: 'Saving...', text: 'Please wait.', timer: 1000, showConfirmButton: false });
        });
    }
});
</script>
@endpush
@endsection
