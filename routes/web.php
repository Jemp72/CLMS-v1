<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SupplyController;
use App\Http\Middleware\RequireLogin;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/switch-role', [AuthController::class, 'switchRole'])->name('switch-role');

Route::middleware(RequireLogin::class)->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook');
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::get('/inventory/print', [InventoryController::class, 'print'])->name('inventory.print');
    
    // QR Code Scanning Route (Requires Login)
    Route::get('/inventory/equipment/{id}', [EquipmentController::class, 'show'])->name('equipment.show');

    // Equipment CRUD
    Route::post('/inventory/equipment', [EquipmentController::class, 'store'])->name('equipment.store');
    Route::post('/inventory/equipment/{id}/status', [EquipmentController::class, 'updateStatus'])->name('equipment.update.status');
    Route::put('/inventory/equipment/{id}', [EquipmentController::class, 'update'])->name('equipment.update');
    Route::delete('/inventory/equipment/{id}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');

    // Supply CRUD
    Route::post('/inventory/supplies', [SupplyController::class, 'store'])->name('supply.store');
    Route::put('/inventory/supplies/{id}', [SupplyController::class, 'update'])->name('supply.update');
    Route::delete('/inventory/supplies/{id}', [SupplyController::class, 'destroy'])->name('supply.destroy');
});