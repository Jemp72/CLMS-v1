<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'class_schedule_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'class_schedule_ids.*' => [
                'integer',
                'exists:class_schedule,class_schedule_id',
            ],

            'csv_file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:2048', // 2 MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'class_schedule_ids.required'  => 'Please select at least one class schedule.',
            'class_schedule_ids.min'       => 'Please select at least one class schedule.',
            'class_schedule_ids.*.exists'  => 'One of the selected class schedules does not exist.',
            'csv_file.required'            => 'Please attach a CSV file.',
            'csv_file.mimes'               => 'Only .csv or .txt files are accepted.',
            'csv_file.max'                 => 'File is too large. Max size is 2 MB.',
        ];
    }
}
