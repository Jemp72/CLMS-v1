<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('bookings')->name('bookings.')->group(function () {
    Route::get('/',                        [BookingController::class, 'index'])->name('index');
    Route::get('/lab/{labId}',             [BookingController::class, 'byLab'])->name('byLab');
    Route::post('/',                       [BookingController::class, 'store'])->name('store');
    Route::get('/{id}',                    [BookingController::class, 'show'])->name('show');
    Route::patch('/{id}/status',           [BookingController::class, 'updateStatus'])->name('updateStatus');
    Route::delete('/{id}',                 [BookingController::class, 'destroy'])->name('destroy');
});
