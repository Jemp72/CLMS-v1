<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
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

// ── Public booking endpoints — no login required ──
// Visitors submit reservation requests without an account.
Route::get('/reserve',  [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

// ── Authenticated app + admin booking management ──
Route::middleware(RequireLogin::class)->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook');
    Route::get('/schedule',         [ScheduleController::class, 'index'])->name('schedule');
    Route::get('/schedule/create',  [ScheduleController::class, 'create'])->name('schedule.create');
    Route::post('/schedule',        [ScheduleController::class, 'store'])->name('schedule.store');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');

    // Booking management lives inside the /schedule view (admin only).
    // Only the approve/reject endpoint is needed.
    Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus'])
        ->name('bookings.updateStatus');
});