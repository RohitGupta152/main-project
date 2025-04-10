<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class showStudentByPayloadRequest extends FormRequest
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
            'id' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Student ID is required.',
            'id.integer' => 'Student ID must be a valid number.',
            // 'id.exists' => 'Student not found in the database.',
        ];
    }

}
