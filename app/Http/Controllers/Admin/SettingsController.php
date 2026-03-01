<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = [
            'site_name' => Setting::get('site_name', config('app.name')),
            'meta_pixel_id' => Setting::get('meta_pixel_id', config('meta.pixel_id')),
            'meta_access_token' => Setting::get('meta_access_token', config('meta.access_token')),
            'seo_default_title' => Setting::get('seo_default_title', config('seo.default_title')),
            'seo_default_description' => Setting::get('seo_default_description', config('seo.default_description')),
            'seo_default_keywords' => Setting::get('seo_default_keywords', ''),
            'gtm_id' => Setting::get('gtm_id', ''),
            'ga_measurement_id' => Setting::get('ga_measurement_id', ''),
            'header_scripts' => Setting::get('header_scripts', ''),
            'footer_scripts' => Setting::get('footer_scripts', ''),
            'meta_verification_scripts' => Setting::get('meta_verification_scripts', ''),
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        Setting::set('site_name', $validated['site_name'] ?? null);
        Setting::set('meta_pixel_id', $validated['meta_pixel_id'] ?? null);
        Setting::set('meta_access_token', $validated['meta_access_token'] ?? null);
        Setting::set('seo_default_title', $validated['seo_default_title'] ?? null);
        Setting::set('seo_default_description', $validated['seo_default_description'] ?? null);
        Setting::set('seo_default_keywords', $validated['seo_default_keywords'] ?? null);
        Setting::set('gtm_id', $validated['gtm_id'] ?? null);
        Setting::set('ga_measurement_id', $validated['ga_measurement_id'] ?? null);
        Setting::set('header_scripts', $validated['header_scripts'] ?? null);
        Setting::set('footer_scripts', $validated['footer_scripts'] ?? null);
        Setting::set('meta_verification_scripts', $validated['meta_verification_scripts'] ?? null);
        ActivityLog::log('settings.updated', 'Settings updated.');
        return redirect()->route('admin.settings.index')->with('success', 'Settings saved.');
    }
}
