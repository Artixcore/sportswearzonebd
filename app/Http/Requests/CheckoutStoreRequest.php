<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckoutStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $advanceAmount = config('checkout.delivery_advance_amount', 150);

        return [
            'name' => 'required|string|min:2|max:255',
            'phone' => ['required', 'string', 'regex:/^01[3-9]\d{8}$/'],
            'city' => 'required|string|max:255',
            'address' => 'required|string|min:10',
            'email' => 'nullable|email',
            'delivery_charge' => 'required|numeric|in:' . $advanceAmount,
            'delivery_advance_confirmed' => 'required|accepted',
            'delivery_advance_method' => 'required|string|in:bKash,Nagad,Rocket,Cash',
            'delivery_advance_txn_id' => 'nullable|string|min:6',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->wantsJson() || $this->ajax()) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first() ?? 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422));
        }
        parent::failedValidation($validator);
    }
}
