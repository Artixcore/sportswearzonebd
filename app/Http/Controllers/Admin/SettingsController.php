<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'meta_pixel_id' => 'nullable|string|max:50',
            'meta_access_token' => 'nullable|string',
            'seo_default_title' => 'nullable|string|max:255',
            'seo_default_description' => 'nullable|string',
        ]);
        Setting::set('site_name', $request->site_name);
        Setting::set('meta_pixel_id', $request->meta_pixel_id);
        Setting::set('meta_access_token', $request->meta_access_token);
        Setting::set('seo_default_title', $request->seo_default_title);
        Setting::set('seo_default_description', $request->seo_default_description);
        return redirect()->route('admin.settings.index')->with('success', 'Settings saved.');
    }
}
