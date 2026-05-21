<?php

namespace App\Http\Controllers;

class InventoryController extends Controller
{
    public function index()
    {
        $equipment = [
            ['serialNumber' => 'PC-2024-001', 'itemName' => 'Desktop Computer', 'brand' => 'Dell', 'model' => 'OptiPlex 7090', 'category' => 'Computer', 'status' => 'available'],
            ['serialNumber' => 'PC-2024-002', 'itemName' => 'Desktop Computer', 'brand' => 'HP', 'model' => 'ProDesk 600', 'category' => 'Computer', 'status' => 'in-use'],
            ['serialNumber' => 'MON-2024-001', 'itemName' => 'LCD Monitor', 'brand' => 'Samsung', 'model' => '24" S24R350', 'category' => 'Monitor', 'status' => 'available'],
            ['serialNumber' => 'KB-2024-001', 'itemName' => 'Keyboard', 'brand' => 'Logitech', 'model' => 'K120', 'category' => 'Peripheral', 'status' => 'available'],
            ['serialNumber' => 'MS-2024-001', 'itemName' => 'Mouse', 'brand' => 'Logitech', 'model' => 'M90', 'category' => 'Peripheral', 'status' => 'in-use'],
            ['serialNumber' => 'SW-2024-001', 'itemName' => 'Network Switch', 'brand' => 'Cisco', 'model' => 'SG250-24', 'category' => 'Network', 'status' => 'available'],
            ['serialNumber' => 'RT-2024-001', 'itemName' => 'Router', 'brand' => 'TP-Link', 'model' => 'Archer AX55', 'category' => 'Network', 'status' => 'in-use'],
            ['serialNumber' => 'PC-2024-003', 'itemName' => 'Desktop Computer', 'brand' => 'Lenovo', 'model' => 'ThinkCentre M720', 'category' => 'Computer', 'status' => 'maintenance'],
            ['serialNumber' => 'MON-2024-002', 'itemName' => 'LCD Monitor', 'brand' => 'LG', 'model' => '24MP400', 'category' => 'Monitor', 'status' => 'damaged'],
            ['serialNumber' => 'PR-2024-001', 'itemName' => 'Printer', 'brand' => 'Epson', 'model' => 'L3210', 'category' => 'Printer', 'status' => 'available'],
        ];

        $categories = ['all', 'Computer', 'Monitor', 'Peripheral', 'Network', 'Printer'];

        return view('inventory.index', compact('equipment', 'categories'));
    }
}