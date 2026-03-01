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
        ];
    }
}
