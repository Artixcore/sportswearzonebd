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
        ActivityLog::log('settings.updated', 'Settings updated.');
        return redirect()->route('admin.settings.index')->with('success', 'Settings saved.');
    }
}
