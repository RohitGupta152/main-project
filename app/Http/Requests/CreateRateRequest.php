<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // public function rules(): array
    // {
    //     return [
    //         // 'user_id' => 'required|numeric',
    //         // 'weight' => 'required|numeric',
    //         // 'rate_amount' => 'required|numeric',
    //     ];
    // }


    public function rules(): array
    {
        return [
            'user_id' => 'required|integer',
            'data' => 'required|array|min:1',
            'data.*.weight' => 'required|numeric|min:0.1',
            'data.*.rate_amount' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'data.*.weight.distinct' => 'Each weight value must be unique within the request.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => $validator->errors()->first(),
        ], 422));
    }

}
