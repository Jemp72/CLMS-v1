<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $activityLogs = [
            ['user' => 'Juan Dela Cruz (2021-12345)', 'action' => 'Logged in',                                'timestamp' => '5 mins ago',  'status' => 'success'],
            ['user' => 'Prof. Reyes',                  'action' => 'Created lab reservation for Lab A',       'timestamp' => '12 mins ago', 'status' => 'success'],
            ['user' => 'Admin',                        'action' => 'Added new equipment: PC-2024-015',        'timestamp' => '25 mins ago', 'status' => 'success'],
            ['user' => 'Maria Santos (2021-12346)',    'action' => 'Logged out',                              'timestamp' => '32 mins ago', 'status' => 'info'],
            ['user' => 'System',                       'action' => 'Low stock alert: Mouse - Logitech M90',   'timestamp' => '1 hour ago',  'status' => 'warning'],
            ['user' => 'Pedro Garcia (2021-12347)',    'action' => 'Logged in',                                'timestamp' => '1 hour ago',  'status' => 'success'],
        ];

        $lowStockItems = [
            ['item' => 'Mouse - Logitech M90',       'category' => 'Peripheral', 'quantity' => 3, 'threshold' => 10, 'status' => 'critical'],
            ['item' => 'Keyboard - Logitech K120',   'category' => 'Peripheral', 'quantity' => 5, 'threshold' => 10, 'status' => 'warning'],
            ['item' => 'HDMI Cable - 2m',            'category' => 'Cable',      'quantity' => 7, 'threshold' => 15, 'status' => 'warning'],
            ['item' => 'Ethernet Cable - Cat6',      'category' => 'Cable',      'quantity' => 8, 'threshold' => 20, 'status' => 'warning'],
        ];

        return view('dashboard.admin', compact('activityLogs', 'lowStockItems'));
    }
}
