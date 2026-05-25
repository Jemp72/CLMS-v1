<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Public reservation form — visitors can submit without login.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lab_id' => [
                'required',
                'integer',
                'exists:laboratories,lab_id',
            ],

            'requestor_name' => [
                'required',
                'string',
                'max:150',
            ],

            'contact_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'purpose' => [
                'required',
                'string',
                'max:255',
            ],

            'booking_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'lab_id.exists'             => 'The selected laboratory does not exist.',
            'booking_date.after_or_equal' => 'You cannot book a date in the past.',
            'end_time.after'            => 'End time must be after the start time.',
        ];
    }
}
