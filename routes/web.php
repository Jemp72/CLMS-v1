<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\ScheduleController;
use App\Http\Middleware\RequireLogin;
use Illuminate\Support\Facades\Route;

// ── Auth ──
Route::get('/login',          [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',         [AuthController::class, 'login']);
Route::post('/logout',        [AuthController::class, 'logout'])->name('logout');
Route::post('/switch-role',   [AuthController::class, 'switchRole'])->name('switch-role');

// ── Public booking — no login required ──
// Visitors submit reservation requests without an account.
Route::get('/reserve',   [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

// ── Public logging terminal — no login required ──
// Kiosk used by students/guests to time-in / time-out without an account.
Route::prefix('logging')->group(function () {
    Route::get('/',                  [LogbookController::class, 'index'])->name('logging.index');
    Route::post('/student/time-in',  [LogbookController::class, 'studentTimeIn'])->name('logging.student.time-in');
    Route::post('/student/time-out', [LogbookController::class, 'studentTimeOut'])->name('logging.student.time-out');
    Route::post('/guest/time-in',    [LogbookController::class, 'guestTimeIn'])->name('logging.guest.time-in');
});

// ── Authenticated app + admin management ──
Route::middleware(RequireLogin::class)->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Admin logbook (lab utilization logs)
    Route::get('/logbook',           [LogbookController::class, 'logs'])->name('logbook');
    Route::get('/logs',              [LogbookController::class, 'logs'])->name('logs.index');
    Route::get('/logs/active-users', [LogbookController::class, 'activeUsers'])->name('logs.active-users');
    Route::get('/logs/{id}',         [LogbookController::class, 'show'])->name('logs.show');

    // Calendar view + class schedule management
    Route::get('/schedule',         [ScheduleController::class, 'index'])->name('schedule');
    Route::get('/schedule/create',  [ScheduleController::class, 'create'])->name('schedule.create');
    Route::post('/schedule',        [ScheduleController::class, 'store'])->name('schedule.store');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');

    // Booking approve / reject (admin action from the /schedule view)
    Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus'])
        ->name('bookings.updateStatus');
});
