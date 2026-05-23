<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\ScheduleController;
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
});