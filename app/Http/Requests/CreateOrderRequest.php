<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'order_id' => 'required|string',
            'customer_name' => 'required|string',
            'email' => 'required|email',

            'contact_no' => 'required|string|max:15',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'pin_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',

            'products' => 'required|array',
            'products.*.product_name' => 'required|string',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.quantity' => 'required|integer|min:1',
            // 'products.*.weight' => 'required|numeric|min:0', // Individual weight
            // 'products.*.length' => 'required|numeric|min:0', // Length for volumetric calculation
            // 'products.*.width' => 'required|numeric|min:0', // Length for volumetric calculation
            // 'products.*.height' => 'required|numeric|min:0', // Height for volumetric calculation
        ];
    }
}
