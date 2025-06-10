<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:menus,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
            'is_featured' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'Menu name already exists',
            'category_id.required' => 'Please select a category',
            'price.min' => 'Price must be greater than 0',
            'stock.min' => 'Stock cannot be negative',
        ];
    }
}
