<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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

        // ── Recent activity (last 8 sign-in / sign-out events) ────
        $activityLogs = DB::table('lab_utilization_logs')
            ->leftJoin('students', 'lab_utilization_logs.student_id', '=', 'students.student_id')
            ->leftJoin('guest_logs', 'lab_utilization_logs.guest_log_id', '=', 'guest_logs.guest_log_id')
            ->leftJoin('laboratories', 'lab_utilization_logs.lab_id', '=', 'laboratories.lab_id')
            ->select(
                'lab_utilization_logs.purpose',
                'lab_utilization_logs.time_in',
                'lab_utilization_logs.time_out',
                'laboratories.lab_name',
                DB::raw("COALESCE(
                    CONCAT(students.first_name, ' ', students.last_name),
                    guest_logs.guest_name
                ) AS person_name"),
                DB::raw("COALESCE(students.student_id, 'GUEST') AS person_id"),
            )
            ->orderByDesc('lab_utilization_logs.time_in')
            ->limit(8)
            ->get()
            ->map(function ($log) {
                $signedOut = !is_null($log->time_out);
                $when      = Carbon::parse($signedOut ? $log->time_out : $log->time_in)->diffForHumans();

                return [
                    'user'      => $log->person_name . ' (' . $log->person_id . ')',
                    'action'    => ($signedOut ? 'Signed out from ' : 'Signed in to ')
                                   . ($log->lab_name ?? 'a lab')
                                   . ($log->purpose ? ' — ' . $log->purpose : ''),
                    'timestamp' => $when,
                    'status'    => $signedOut ? 'info' : 'success',
                ];
            })
            ->toArray();

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
            'activityLogs',
            'lowStockItems',
        ));
    }
}
