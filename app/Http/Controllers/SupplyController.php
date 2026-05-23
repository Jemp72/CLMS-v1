<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplyController extends Controller
{
    public const CATEGORIES = [
        'Stationery',
        'Cleaning',
        'Ink & Toner',
        'Cables',
        'Tools',
        'Other',
    ];

    public function store(Request $request)
    {
        if (session('role') !== 'admin') {
            abort(403);
        }

        $data = $request->validate([
            'supply_name'             => 'required|string|max:100',
            'category'                => 'required|in:Stationery,Cleaning,Ink & Toner,Cables,Tools,Other',
            'quantity'                => 'required|integer|min:0',
            'minimum_stock_threshold' => 'required|integer|min:0',
            'unit'                    => 'nullable|string|max:30',
            'remarks'                 => 'nullable|string|max:255',
        ]);

        DB::table('office_supplies')->insert($data);

        return redirect()->route('inventory', ['tab' => 'supplies'])
            ->with('success', 'Supply item added successfully.');
    }

    public function update(Request $request, int $id)
    {
        if (session('role') !== 'admin') {
            abort(403);
        }

        $data = $request->validate([
            'supply_name'             => 'required|string|max:100',
            'category'                => 'required|in:Stationery,Cleaning,Ink & Toner,Cables,Tools,Other',
            'quantity'                => 'required|integer|min:0',
            'minimum_stock_threshold' => 'required|integer|min:0',
            'unit'                    => 'nullable|string|max:30',
            'remarks'                 => 'nullable|string|max:255',
        ]);

        DB::table('office_supplies')->where('supply_id', $id)->update($data);

        return redirect()->route('inventory', ['tab' => 'supplies'])
            ->with('success', 'Supply item updated successfully.');
    }

    public function destroy(int $id)
    {
        if (session('role') !== 'admin') {
            abort(403);
        }

        DB::table('office_supplies')->where('supply_id', $id)->delete();

        return redirect()->route('inventory', ['tab' => 'supplies'])
            ->with('success', 'Supply item deleted.');
    }
}
