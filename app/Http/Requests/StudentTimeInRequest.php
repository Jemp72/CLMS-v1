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
                'exists:students,student_id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.exists' => 'No student found with that number.',
        ];
    }
}
