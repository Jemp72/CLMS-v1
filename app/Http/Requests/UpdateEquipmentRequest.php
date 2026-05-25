<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'equipment_no' => [
                'required', 'string', 'max:50',
                Rule::unique('equipments', 'equipment_no')->ignore($id, 'equipment_id'),
            ],
            'serial_no' => [
                'nullable', 'string', 'max:100',
                Rule::unique('equipments', 'serial_no')->ignore($id, 'equipment_id'),
            ],

            'equipment_name' => ['required', 'string', 'max:150'],
            'brand'          => ['nullable', 'string', 'max:100'],
            'model_number'   => ['nullable', 'string', 'max:100'],

            'equipment_type'   => ['required', 'in:computer_unit,peripheral,component,miscellaneous'],
            'equipment_status' => ['required', 'in:available,in-use,maintenance,damaged'],

            'quantity'            => ['required', 'integer', 'min:1'],
            'lab_id'              => ['required', 'integer', 'exists:laboratories,lab_id'],
            'parent_equipment_id' => ['nullable', 'integer', 'exists:equipments,equipment_id'],

            'preventive_maintenance_done' => ['sometimes', 'boolean'],
            'calibration_done'            => ['sometimes', 'boolean'],

            'remarks' => ['nullable', 'string', 'max:255'],
        ];
    }
}
