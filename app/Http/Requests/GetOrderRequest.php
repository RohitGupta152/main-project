<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'id' => 'required|integer'

            'order_id' => 'nullable|string', //array
            'customer_name' => 'nullable|string',
            'created_date' => 'nullable|string|regex:/^\d{4}-\d{2}-\d{2} \d{4}-\d{2}-\d{2}$/', //regex:/^\d{2} \d{2} \d{4} \d{2} \d{2} \d{4}$/'
        ];
    
    }


    public function messages(): array
    {
        return [
            'created_date.regex' => 'The date format must be: yyyy-mm-dd yyyy-mm-dd (e.g., 2025-03-01 2025-03-10).',
        ];
    }


}
