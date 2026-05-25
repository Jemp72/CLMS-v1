<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplyRequest;
use App\Http\Requests\UpdateSupplyRequest;
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

    public const STATUSES = [
        'fully_stocked',
        'in_stock',
        'low_stock',
        'out_of_stock',
    ];

    public function store(StoreSupplyRequest $request)
    {
        DB::table('office_supplies')->insert($request->validated());

        return redirect()->route('inventory', ['tab' => 'supplies'])
            ->with('success', 'Supply item added successfully.');
    }

    public function update(UpdateSupplyRequest $request, int $id)
    {
        DB::table('office_supplies')
            ->where('supply_id', $id)
            ->update($request->validated());

        return redirect()->route('inventory', ['tab' => 'supplies'])
            ->with('success', 'Supply item updated successfully.');
    }

    public function destroy(int $id)
    {
        DB::table('office_supplies')->where('supply_id', $id)->delete();

        return redirect()->route('inventory', ['tab' => 'supplies'])
            ->with('success', 'Supply item deleted.');
    }
}
