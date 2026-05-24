<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentTimeInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => [
                'required',
                'exists:students,student_id'
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

            'instructor_id' => [
                'nullable',
                'exists:system_users,system_user_id'
            ],
        ];
    }
}