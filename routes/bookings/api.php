<?php

use App\Http\Controllers\Bookings\BookingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('bookings')->middleware('auth:sanctum', 'auth:users')->group(function () {
    Route::get('/', [BookingsController::class, 'index'])->name('bookings.index');
    Route::post('/', [BookingsController::class, 'store'])->name('bookings.store');
});
