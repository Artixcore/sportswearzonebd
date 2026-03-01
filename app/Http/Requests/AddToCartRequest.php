<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->input('product_id');
        $product = $productId ? \App\Models\Product::find($productId) : null;
        $allowedSizes = $product ? \App\Models\Product::allowedSizesFor($product->size_type ?? 'standard') : ['S', 'M', 'L', 'XL', 'XXL'];
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'size' => 'required|string|in:' . implode(',', $allowedSizes),
        ];
    }
}
