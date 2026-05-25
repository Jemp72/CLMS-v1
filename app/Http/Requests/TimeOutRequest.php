<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TimeOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => [
                'required',
                'string',
                'max:150',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'Please enter your student number or full name.',
        ];
    }
}
