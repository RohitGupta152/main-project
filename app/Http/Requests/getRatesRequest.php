<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class getRatesRequest extends FormRequest
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
            'user_id' => 'required|numeric',
            'weight' => 'nullable|numeric',
            // 'rate_amount' => 'nullable|numeric',
            'created_date' => 'nullable|string|regex:/^\d{4}-\d{2}-\d{2} \d{4}-\d{2}-\d{2}$/',
        ];
    }

    public function messages(): array 
    {
        return[
            // 'user_id.required' => 'User ID is required', 
            // 'user_id.numeric' => 'User ID must be a number',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'Rate chart users id ',
            'weight' => 'Weight (kg)',
        ];
    }
}
