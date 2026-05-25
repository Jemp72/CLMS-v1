<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuestTimeInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guest_name' => [
                'required',
                'string',
                'max:150',
            ],

            'booked_under' => [
                'required',
                'string',
                'max:150',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'guest_name.required'   => 'Please enter your full name.',
            'booked_under.required' => 'Please enter the name on the reservation.',
        ];
    }
}
