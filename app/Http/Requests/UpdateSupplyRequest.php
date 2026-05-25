<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supply_name' => ['required', 'string', 'max:100'],
            'category'    => ['required', 'in:Stationery,Cleaning,Ink & Toner,Cables,Tools,Other'],
            'status'      => ['required', 'in:fully_stocked,in_stock,low_stock,out_of_stock'],
            'unit'        => ['nullable', 'string', 'max:30'],
            'remarks'     => ['nullable', 'string', 'max:255'],
        ];
    }
}
