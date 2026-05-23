<?php

namespace App\Http\Controllers;

use App\Http\Controllers\EquipmentController;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        // ── Equipment ─────────────────────────────────────────────────────────
        $equipment = DB::table('equipments')
            ->join('laboratories', 'equipments.lab_id', '=', 'laboratories.lab_id')
            ->select(
                'equipments.*',
                'laboratories.lab_name'
            )
            ->orderBy('equipment_name')
            ->get()
            ->map(fn ($e) => (array) $e)
            ->toArray();

        // ── Supplies ──────────────────────────────────────────────────────────
        $supplies = DB::table('office_supplies')
            ->orderBy('supply_name')
            ->get()
            ->map(fn ($s) => (array) $s)
            ->toArray();

        // ── Stats ─────────────────────────────────────────────────────────────
        $totalEquipment       = count($equipment);
        $totalSupplies        = count($supplies);
        $lowStockCount        = count(array_filter($supplies, fn ($s) => $s['quantity'] <= $s['minimum_stock_threshold']));
        $needsMaintenanceCount = count(array_filter($equipment, fn ($e) => ! $e['preventive_maintenance_done']));

        // Equipment count per laboratory
        $perLab = DB::table('equipments')
            ->join('laboratories', 'equipments.lab_id', '=', 'laboratories.lab_id')
            ->select('laboratories.lab_name', DB::raw('COUNT(*) as total'))
            ->groupBy('laboratories.lab_id', 'laboratories.lab_name')
            ->orderBy('laboratories.lab_name')
            ->get()
            ->toArray();

        // ── Filter options ────────────────────────────────────────────────────
        $labs = DB::table('laboratories')
            ->select('lab_id', 'lab_name')
            ->orderBy('lab_name')
            ->get()
            ->toArray();

        $typeLabels = EquipmentController::TYPE_LABELS;
        $categories = SupplyController::CATEGORIES;

        return view('inventory.index', compact(
            'equipment',
            'supplies',
            'totalEquipment',
            'totalSupplies',
            'lowStockCount',
            'needsMaintenanceCount',
            'perLab',
            'labs',
            'typeLabels',
            'categories',
        ));
    }

    public function print()
    {
        // Equipment grouped by type then lab
        $equipmentByType = [];
        $rows = DB::table('equipments')
            ->join('laboratories', 'equipments.lab_id', '=', 'laboratories.lab_id')
            ->select('equipments.*', 'laboratories.lab_name')
            ->orderBy('equipment_type')
            ->orderBy('lab_name')
            ->orderBy('equipment_name')
            ->get();

        foreach ($rows as $row) {
            $type = EquipmentController::TYPE_LABELS[$row->equipment_type] ?? $row->equipment_type;
            $equipmentByType[$type][] = (array) $row;
        }

        // Supplies grouped by category
        $suppliesByCategory = [];
        $supplyRows = DB::table('office_supplies')
            ->orderBy('category')
            ->orderBy('supply_name')
            ->get();

        foreach ($supplyRows as $row) {
            $suppliesByCategory[$row->category][] = (array) $row;
        }

        return view('inventory.print', compact('equipmentByType', 'suppliesByCategory'));
    }
}