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
        $lowStockCount        = count(array_filter($supplies, fn ($s) => in_array($s['status'], ['low_stock', 'out_of_stock'])));

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
            'perLab',
            'labs',
            'typeLabels',
            'categories',
        ));
    }

    public function print(\Illuminate\Http\Request $request)
    {
        $tab = $request->query('tab', 'equipment');

        $equipmentRows = [];
        $suppliesRows = [];

        if ($tab === 'equipment') {
            $equipmentRows = DB::table('equipments')
                ->join('laboratories', 'equipments.lab_id', '=', 'laboratories.lab_id')
                ->select('equipments.*', 'laboratories.lab_name')
                ->orderBy('equipment_name')
                ->get()
                ->map(fn($e) => (array) $e)
                ->toArray();
        } else {
            $suppliesRows = DB::table('office_supplies')
                ->orderBy('supply_name')
                ->get()
                ->map(fn($s) => (array) $s)
                ->toArray();
        }

        return view('inventory.print', compact('tab', 'equipmentRows', 'suppliesRows'));
    }
}