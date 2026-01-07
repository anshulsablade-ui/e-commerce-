<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // allow authenticated users (adjust if needed)
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',

            'category' => 'required|array|min:1',
            'category.*' => 'required|exists:categories,id',

            'product' => 'required|array|min:1',
            'product.*' => 'required|exists:products,id',

            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',

            'price' => 'required|array',
            'price.*' => 'required|numeric|min:0',

            'discount' => 'nullable|numeric|min:0|max:100',

            'order_status' => 'required|in:pending,processing,completed,cancelled',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Please select a customer.',
            'customer_id.exists' => 'Selected customer does not exist.',

            'category.required' => 'Please select at least one category.',
            'category.array' => 'Invalid category data.',
            'category.*.required' => 'Category is required for this product row.',
            'category.*.exists' => 'Selected category is invalid.',

            'product.required' => 'Please add at least one product.',
            'product.array' => 'Invalid product data.',
            'product.min' => 'At least one product is required.',
            'product.*.required' => 'Product is required.',
            'product.*.exists' => 'Selected product does not exist.',

            'quantity.required' => 'Quantity is required.',
            'quantity.array' => 'Invalid quantity data.',
            'quantity.*.required' => 'Quantity is required.',
            'quantity.*.integer' => 'Quantity must be a whole number.',
            'quantity.*.min' => 'Quantity must be at least 1.',

            'price.required' => 'Price is required.',
            'price.array' => 'Invalid price data.',
            'price.*.required' => 'Price is required.',
            'price.*.numeric' => 'Price must be a valid number.',
            'price.*.min' => 'Price cannot be negative.',

            'discount.numeric' => 'Discount must be a number.',
            'discount.min' => 'Discount cannot be less than 0%.',
            'discount.max' => 'Discount cannot be more than 100%.',

            'order_status.required' => 'Please select order status.',
            'order_status.in' => 'Invalid order status selected.',
        ];
    }

    /**
     * Return JSON validation response (for AJAX)
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException(
            $validator,
            response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
