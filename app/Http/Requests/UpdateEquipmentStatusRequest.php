<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'equipment_status' => ['required', 'in:available,in-use,maintenance,damaged'],

            'preventive_maintenance_done' => ['sometimes', 'boolean'],
            'calibration_done'            => ['sometimes', 'boolean'],
        ];
    }
}
