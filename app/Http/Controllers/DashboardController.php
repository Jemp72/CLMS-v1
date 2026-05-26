<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Stat cards ─────────────────────────────────────────────
        $totalStudents  = DB::table('students')->whereNull('deleted_at')->count();
        $activeNow      = DB::table('lab_utilization_logs')->whereNull('time_out')->count();
        $totalEquipment = DB::table('equipments')->count();
        $lowStockCount  = DB::table('office_supplies')
            ->whereIn('status', ['low_stock', 'out_of_stock'])
            ->count();

        // ── Inventory alerts (low / out of stock supplies) ────────
        $lowStockItems = DB::table('office_supplies')
            ->whereIn('status', ['low_stock', 'out_of_stock'])
            ->orderByRaw("FIELD(status, 'out_of_stock', 'low_stock')")
            ->orderBy('supply_name')
            ->limit(6)
            ->get()
            ->map(fn ($s) => [
                'item'     => $s->supply_name,
                'category' => $s->category,
                'status'   => $s->status === 'out_of_stock' ? 'critical' : 'warning',
                'label'    => $s->status === 'out_of_stock' ? 'Out of Stock' : 'Low Stock',
            ])
            ->toArray();

        return view('dashboard.admin', compact(
            'totalStudents',
            'activeNow',
            'totalEquipment',
            'lowStockCount',
            'lowStockItems',
        ));
    }
}
