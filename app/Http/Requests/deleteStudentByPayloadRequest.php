<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class deleteStudentByPayloadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Set to true to allow all requests
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|' //exists:students,id
        ];
    }
}
