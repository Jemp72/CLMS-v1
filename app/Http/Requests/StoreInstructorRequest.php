<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'  => ['required', 'string', 'max:50'],
            'last_name'   => ['required', 'string', 'max:50'],
            'middle_name' => ['nullable', 'string', 'max:50'],
            'suffix'      => ['nullable', 'string', 'max:5'],

            'email' => [
                'nullable',
                'email',
                'max:100',
                'unique:instructors,email',
            ],

            'contact_number' => ['nullable', 'string', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'An instructor with this email already exists.',
        ];
    }
}
