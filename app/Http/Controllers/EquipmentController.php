<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;
use App\Http\Requests\UpdateEquipmentStatusRequest;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    // Display map: DB ENUM value → friendly label
    public const TYPE_LABELS = [
        'computer_unit' => 'Computer Unit',
        'peripheral'    => 'Peripheral',
        'component'     => 'Component',
        'miscellaneous' => 'Miscellaneous',
    ];

    public function show(int $id)
    {
        $equipment = DB::table('equipments')
            ->join('laboratories', 'equipments.lab_id', '=', 'laboratories.lab_id')
            ->select('equipments.*', 'laboratories.lab_name')
            ->where('equipment_id', $id)
            ->firstOrFail();

        return view('inventory.equipment.show', [
            'equipment'  => $equipment,
            'typeLabels' => self::TYPE_LABELS,
        ]);
    }

    public function store(StoreEquipmentRequest $request)
    {
        $data = $request->validated();

        // Checkbox booleans default to false when unchecked
        $data['preventive_maintenance_done'] = $request->has('preventive_maintenance_done') ? 1 : 0;
        $data['calibration_done']            = $request->has('calibration_done') ? 1 : 0;

        DB::table('equipments')->insert($data);

        return redirect()->route('inventory', ['tab' => 'equipment'])
            ->with('success', 'Equipment added successfully.');
    }

    public function update(UpdateEquipmentRequest $request, int $id)
    {
        $data = $request->validated();

        $data['preventive_maintenance_done'] = $request->has('preventive_maintenance_done') ? 1 : 0;
        $data['calibration_done']            = $request->has('calibration_done') ? 1 : 0;

        DB::table('equipments')->where('equipment_id', $id)->update($data);

        return redirect()->route('inventory', ['tab' => 'equipment'])
            ->with('success', 'Equipment updated successfully.');
    }

    public function updateStatus(UpdateEquipmentStatusRequest $request, int $id)
    {
        $data = $request->validated();

        $data['preventive_maintenance_done'] = $request->has('preventive_maintenance_done') ? 1 : 0;
        $data['calibration_done']            = $request->has('calibration_done') ? 1 : 0;

        DB::table('equipments')->where('equipment_id', $id)->update($data);

        return redirect()->route('equipment.show', $id)
            ->with('scan_success', 'Status updated to ' . ucfirst(str_replace('-', ' ', $data['equipment_status'])) . '.');
    }

    public function destroy(int $id)
    {
        DB::table('equipments')->where('equipment_id', $id)->delete();

        return redirect()->route('inventory', ['tab' => 'equipment'])
            ->with('success', 'Equipment deleted.');
    }
}
