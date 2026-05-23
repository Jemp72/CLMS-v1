<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    // Display map: DB ENUM value → friendly label
    public const TYPE_LABELS = [
        'computer_unit'  => 'Computer Unit',
        'peripheral'     => 'Peripheral',
        'component'      => 'Component',
        'miscellaneous'  => 'Miscellaneous',
    ];

    public function show(int $id)
    {
        $equipment = DB::table('equipments')
            ->join('laboratories', 'equipments.lab_id', '=', 'laboratories.lab_id')
            ->select(
                'equipments.*',
                'laboratories.lab_name'
            )
            ->where('equipment_id', $id)
            ->firstOrFail();

        return view('inventory.equipment.show', [
            'equipment'  => $equipment,
            'typeLabels' => self::TYPE_LABELS,
        ]);
    }

    public function store(Request $request)
    {
        if (session('role') !== 'admin') {
            abort(403);
        }

        $data = $request->validate([
            'equipment_no'                => 'required|string|max:50',
            'serial_no'                   => 'nullable|string|max:100|unique:equipments,serial_no',
            'equipment_name'              => 'required|string|max:150',
            'brand'                       => 'nullable|string|max:100',
            'model_number'                => 'nullable|string|max:100',
            'equipment_type'              => 'required|in:computer_unit,peripheral,component,miscellaneous',
            'equipment_status'            => 'required|in:available,in-use,maintenance,damaged',
            'quantity'                    => 'required|integer|min:1',
            'lab_id'                      => 'required|integer|exists:laboratories,lab_id',
            'parent_equipment_id'         => 'nullable|integer|exists:equipments,equipment_id',
            'preventive_maintenance_done' => 'nullable|boolean',
            'remarks'                     => 'nullable|string|max:255',
        ]);

        $data['preventive_maintenance_done'] = $request->boolean('preventive_maintenance_done');

        DB::table('equipments')->insert($data);

        return redirect()->route('inventory', ['tab' => 'equipment'])
            ->with('success', 'Equipment added successfully.');
    }

    public function update(Request $request, int $id)
    {
        if (session('role') !== 'admin') {
            abort(403);
        }

        $data = $request->validate([
            'equipment_no'                => 'required|string|max:50',
            'serial_no'                   => 'nullable|string|max:100|unique:equipments,serial_no,' . $id . ',equipment_id',
            'equipment_name'              => 'required|string|max:150',
            'brand'                       => 'nullable|string|max:100',
            'model_number'                => 'nullable|string|max:100',
            'equipment_type'              => 'required|in:computer_unit,peripheral,component,miscellaneous',
            'equipment_status'            => 'required|in:available,in-use,maintenance,damaged',
            'quantity'                    => 'required|integer|min:1',
            'lab_id'                      => 'required|integer|exists:laboratories,lab_id',
            'parent_equipment_id'         => 'nullable|integer|exists:equipments,equipment_id',
            'preventive_maintenance_done' => 'nullable|boolean',
            'remarks'                     => 'nullable|string|max:255',
        ]);

        $data['preventive_maintenance_done'] = $request->boolean('preventive_maintenance_done');

        DB::table('equipments')->where('equipment_id', $id)->update($data);

        return redirect()->route('inventory', ['tab' => 'equipment'])
            ->with('success', 'Equipment updated successfully.');
    }

    public function destroy(int $id)
    {
        if (session('role') !== 'admin') {
            abort(403);
        }

        DB::table('equipments')->where('equipment_id', $id)->delete();

        return redirect()->route('inventory', ['tab' => 'equipment'])
            ->with('success', 'Equipment deleted.');
    }
}
