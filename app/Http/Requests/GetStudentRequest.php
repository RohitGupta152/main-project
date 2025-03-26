<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use function PHPUnit\Framework\returnValueMap;

class GetStudentRequest extends FormRequest
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
            'user_id' => 'nullable|numeric',
            'name' => 'nullable|string',
            'email' => 'nullable|string',
            'age' => 'nullable|numeric',
            'course' => 'nullable|string',
        ];
    }

    public function attributes():array
    {
        return[

            "user_id" => "Student User ID"

        ];

    }
}
