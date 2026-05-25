<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (session('role') === 'instructor') {
            return $this->instructorDashboard();
        }

        return $this->adminDashboard();
    }

    private function adminDashboard()
    {
        $activityLogs = [
            ['user' => 'Juan Dela Cruz (2021-12345)', 'action' => 'Logged in', 'timestamp' => '5 mins ago', 'status' => 'success'],
            ['user' => 'Prof. Reyes', 'action' => 'Created lab reservation for Lab A', 'timestamp' => '12 mins ago', 'status' => 'success'],
            ['user' => 'Admin', 'action' => 'Added new equipment: PC-2024-015', 'timestamp' => '25 mins ago', 'status' => 'success'],
            ['user' => 'Maria Santos (2021-12346)', 'action' => 'Logged out', 'timestamp' => '32 mins ago', 'status' => 'info'],
            ['user' => 'System', 'action' => 'Low stock alert: Mouse - Logitech M90', 'timestamp' => '1 hour ago', 'status' => 'warning'],
            ['user' => 'Pedro Garcia (2021-12347)', 'action' => 'Logged in', 'timestamp' => '1 hour ago', 'status' => 'success'],
        ];

        $totalEquipment = DB::table('equipments')->count();

        $lowStockItems = DB::table('office_supplies')
            ->whereColumn('quantity', '<=', 'minimum_stock_threshold')
            ->orderBy('quantity', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($s) {
                return [
                    'item' => $s->supply_name,
                    'category' => $s->category,
                    'quantity' => $s->quantity,
                    'threshold' => $s->minimum_stock_threshold,
                    'status' => $s->quantity == 0 ? 'critical' : 'warning',
                    'percentage' => $s->minimum_stock_threshold > 0 ? round(($s->quantity / $s->minimum_stock_threshold) * 100) : 0,
                ];
            })
            ->toArray();

        return view('dashboard.admin', compact('activityLogs', 'lowStockItems', 'totalEquipment'));
    }

    private function instructorDashboard()
    {
        $students = [
            ['studentId' => '2021-12345', 'name' => 'Juan Dela Cruz', 'email' => 'jdelacruz@usep.edu.ph', 'program' => 'BS Computer Science', 'status' => 'logged-in', 'lastActive' => '5 mins ago'],
            ['studentId' => '2021-12346', 'name' => 'Maria Santos', 'email' => 'msantos@usep.edu.ph', 'program' => 'BS Information Technology', 'status' => 'logged-in', 'lastActive' => '12 mins ago'],
            ['studentId' => '2021-12347', 'name' => 'Pedro Garcia', 'email' => 'pgarcia@usep.edu.ph', 'program' => 'BS Computer Science', 'status' => 'not-logged-in', 'lastActive' => '2 days ago'],
            ['studentId' => '2021-12348', 'name' => 'Ana Reyes', 'email' => 'areyes@usep.edu.ph', 'program' => 'BS Information Technology', 'status' => 'logged-in', 'lastActive' => '18 mins ago'],
            ['studentId' => '2021-12349', 'name' => 'Carlos Lopez', 'email' => 'clopez@usep.edu.ph', 'program' => 'BS Computer Science', 'status' => 'not-logged-in', 'lastActive' => '1 day ago'],
            ['studentId' => '2021-12350', 'name' => 'Sofia Mendoza', 'email' => 'smendoza@usep.edu.ph', 'program' => 'BS Information Technology', 'status' => 'logged-in', 'lastActive' => '25 mins ago'],
            ['studentId' => '2021-12351', 'name' => 'Miguel Torres', 'email' => 'mtorres@usep.edu.ph', 'program' => 'BS Computer Science', 'status' => 'not-logged-in', 'lastActive' => '3 days ago'],
            ['studentId' => '2021-12352', 'name' => 'Isabella Cruz', 'email' => 'icruz@usep.edu.ph', 'program' => 'BS Information Technology', 'status' => 'logged-in', 'lastActive' => '8 mins ago'],
            ['studentId' => '2021-12353', 'name' => 'Diego Ramos', 'email' => 'dramos@usep.edu.ph', 'program' => 'BS Computer Science', 'status' => 'not-logged-in', 'lastActive' => '5 days ago'],
            ['studentId' => '2021-12354', 'name' => 'Lucia Fernandez', 'email' => 'lfernandez@usep.edu.ph', 'program' => 'BS Information Technology', 'status' => 'logged-in', 'lastActive' => '32 mins ago'],
        ];

        return view('dashboard.instructor', compact('students'));
    }
}
