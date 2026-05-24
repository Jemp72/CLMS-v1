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
    Route::get('/logbook', [LogbookController::class, 'logs'])->name('logbook');
    Route::get('/logs', [LogbookController::class, 'logs'])->name('logs.index');
    Route::get('/logs/active-users', [LogbookController::class, 'activeUsers'])->name('logs.active-users');
    Route::get('/logs/{id}', [LogbookController::class, 'show'])->name('logs.show');
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
});

Route::prefix('logging')->group(function () {

    Route::get('/', [LogbookController::class, 'index'])
        ->name('logging.index');

    Route::post('/student/time-in', [LogbookController::class, 'studentTimeIn'])
        ->name('logging.student.time-in');

    Route::post('/student/time-out', [LogbookController::class, 'studentTimeOut'])
        ->name('logging.student.time-out');

    Route::post('/guest/time-in', [LogbookController::class, 'guestTimeIn'])
        ->name('logging.guest.time-in');
});