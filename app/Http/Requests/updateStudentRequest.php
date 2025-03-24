<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateStudentRequest extends FormRequest
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
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $this->id, // Student Give a email first time then it will be Same email is working but Id should be Right Student Id.
            'age'   => 'required|integer|min:1|max:100',
            'course'=> 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Student name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
            'email.unique' => 'This email is already taken.', // Custom validation message for other Student email check
            'age.required' => 'Age is required.',
            'age.integer' => 'Age must be a number.',
            'age.min' => 'Age must be at least 18 years old.', // Custom validation message
            'age.max' => 'Age cannot exceed 100 years.',
            'course.required' => 'Course is required.'
        ];
    }

}
