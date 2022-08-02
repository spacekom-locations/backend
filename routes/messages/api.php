<?php

use App\Http\Controllers\Messages\ThreadsController;
use Illuminate\Support\Facades\Route;

Route::prefix('messages')->middleware('auth:sanctum', 'auth:users')->group(function () {
    Route::get('/', [ThreadsController::class, 'index'])->name('messages.threads.index');
    Route::post('/', [ThreadsController::class, 'store'])->name('messages.threads.store');
    Route::get('/{id}', [ThreadsController::class, 'show'])->name('messages.threads.show');
    Route::post('/compose-from-booking', [ThreadsController::class, 'composeFromBooking'])->name('messages.threads.compose_from_booking');
    Route::post('/{id}', [ThreadsController::class, 'sendMessage'])->name('messages.threads.sendMessage');
});
