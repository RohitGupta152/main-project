<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentByPayloadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Change to true to allow the request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id'    => 'required|',                           //exists:students,id
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:students,email,' . $this->id,       //unique:students,email other Student email ko check kare ga
            'age'   => 'sometimes|integer|min:18|max:100',
            'course'=> 'sometimes|string|max:255'
        ];
    }
}
