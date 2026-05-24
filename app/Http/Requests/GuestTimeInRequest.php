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
                'max:150'
            ],

            'organization' => [
                'nullable',
                'string',
                'max:150'
            ],

            'contact_number' => [
                'nullable',
                'string',
                'max:30'
            ],

            'purpose' => [
                'required',
                'string',
                'max:255'
            ],

            'lab_id' => [
                'required',
                'exists:laboratories,lab_id'
            ],
        ];
    }
}