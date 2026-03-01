<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $variant = $this->route('variant');
        $product = $variant ? $variant->product : $this->route('product');
        $allowedSizes = $product ? \App\Models\Product::allowedSizesFor($product->size_type ?? 'standard') : ['S', 'M', 'L', 'XL', 'XXL'];
        return [
            'name' => 'required|string|max:255',
            'size' => 'required|string|in:' . implode(',', $allowedSizes),
            'color' => 'nullable|string|max:50',
            'sku' => 'required|string|max:100|unique:product_variants,sku,' . $variant->id,
            'price_adjustment' => 'nullable|numeric',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ];
    }
}
