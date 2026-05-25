<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SupplyController;
use App\Http\Middleware\RequireLogin;
use Illuminate\Support\Facades\Route;

// ── Auth ──
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Public booking — no login required ──
// Visitors submit reservation requests without an account.
Route::get('/reserve',   [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

// ── Public logging terminal — no login required ──
// Kiosk used by students/guests to time-in / time-out without an account.
Route::prefix('logging')->group(function () {
    Route::get('/',                 [LogbookController::class, 'index'])->name('logging.index');
    Route::post('/student/time-in', [LogbookController::class, 'studentTimeIn'])->name('logging.student.time-in');
    Route::post('/guest/time-in',   [LogbookController::class, 'guestTimeIn'])->name('logging.guest.time-in');
    Route::post('/time-out',        [LogbookController::class, 'timeOut'])->name('logging.time-out');
});

// ── Authenticated app + admin management ──
Route::middleware(RequireLogin::class)->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Admin logbook (lab utilization logs)
    Route::get('/logbook', [LogbookController::class, 'logs'])->name('logbook');
    Route::get('/logs',    [LogbookController::class, 'logs'])->name('logs.index');

    // Calendar view + class schedule management
    Route::get('/schedule',        [ScheduleController::class, 'index'])->name('schedule');
    Route::get('/schedule/create', [ScheduleController::class, 'create'])->name('schedule.create');
    Route::post('/schedule',       [ScheduleController::class, 'store'])->name('schedule.store');

    // Class lists — view enrolled students + CSV import
    Route::get('/class-lists',         [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('/class-lists/import',  [EnrollmentController::class, 'create'])->name('enrollments.import');
    Route::post('/class-lists/import', [EnrollmentController::class, 'store'])->name('enrollments.store');

    // Instructors — managed independently of system users
    Route::get('/instructors',         [InstructorController::class, 'index'])->name('instructors.index');
    Route::get('/instructors/create',  [InstructorController::class, 'create'])->name('instructors.create');
    Route::post('/instructors',        [InstructorController::class, 'store'])->name('instructors.store');
    Route::delete('/instructors/{id}', [InstructorController::class, 'destroy'])->name('instructors.destroy');

    // Inventory
    Route::get('/inventory',       [InventoryController::class, 'index'])->name('inventory');
    Route::get('/inventory/print', [InventoryController::class, 'print'])->name('inventory.print');

    // Equipment CRUD (admin only enforced inside the controller)
    Route::get('/inventory/equipment/{id}',         [EquipmentController::class, 'show'])->name('equipment.show');
    Route::post('/inventory/equipment',             [EquipmentController::class, 'store'])->name('equipment.store');
    Route::post('/inventory/equipment/{id}/status', [EquipmentController::class, 'updateStatus'])->name('equipment.update.status');
    Route::put('/inventory/equipment/{id}',         [EquipmentController::class, 'update'])->name('equipment.update');
    Route::delete('/inventory/equipment/{id}',      [EquipmentController::class, 'destroy'])->name('equipment.destroy');

    // Supply CRUD
    Route::post('/inventory/supplies',        [SupplyController::class, 'store'])->name('supply.store');
    Route::put('/inventory/supplies/{id}',    [SupplyController::class, 'update'])->name('supply.update');
    Route::delete('/inventory/supplies/{id}', [SupplyController::class, 'destroy'])->name('supply.destroy');

    // Booking approve / reject (admin action from the /schedule view)
    Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus'])
        ->name('bookings.updateStatus');
});
