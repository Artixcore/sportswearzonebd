<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_name' => 'nullable|string|max:255',
            'meta_pixel_id' => 'nullable|string|max:50',
            'meta_access_token' => 'nullable|string',
            'seo_default_title' => 'nullable|string|max:255',
            'seo_default_description' => 'nullable|string',
            'seo_default_keywords' => 'nullable|string|max:512',
            'gtm_id' => ['nullable', 'string', 'regex:/^GTM-[A-Z0-9]{4,}$/i'],
            'ga_measurement_id' => 'nullable|string|max:50',
            'header_scripts' => 'nullable|string',
            'footer_scripts' => 'nullable|string',
            'meta_verification_scripts' => 'nullable|string',
        ];
    }
}
