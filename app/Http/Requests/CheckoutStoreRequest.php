<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'phone' => ['required', 'string', 'regex:/^01[3-9]\d{8}$/'],
            'city' => 'required|string|max:255',
            'address' => 'required|string|min:10',
            'email' => 'nullable|email',
        ];
    }
}
